<?php
/**
 * Controller pro AJAX požadavky modulu Mezistranka hodnoceni
 */

class Mezistranka_HodnoceniAjaxModuleFrontController extends ModuleFrontController
{
    /**
     * Přidat hlavičky pro AJAX odpovědi
     */
    private function sendJSONHeaders()
    {
        header('Content-Type: application/json');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
    }

    public function initContent()
    {
        parent::initContent();
        
        $action = Tools::getValue('action', '');
        
        if ($action === 'getHodnoceniForm') {
            $this->getHodnoceniForm();
        } elseif ($action === 'submitRating') {
            $this->submitRating();
        } else {
            $this->sendJSONHeaders();
            die(json_encode([
                'success' => false,
                'error' => 'Neznámá akce',
                'action' => $action
            ]));
        }
    }
    
    /**
     * Vrátí HTML formuláře pro hodnocení
     */
    private function getHodnoceniForm()
    {
        $module = Module::getInstanceByName('mezistranka_hodnoceni');
        
        // Nastavit proměnné pro šablonu
        $this->context->smarty->assign([
            'page_title' => $module->l('Ohodnoťte nás'),
            'module_dir' => $module->getPathUri(),
        ]);
        
        // Vrátit obsah šablony
        $content = $module->fetch('module:mezistranka_hodnoceni/views/templates/front/rating_content.tpl');
        
        $this->sendJSONHeaders();
        die(json_encode([
            'success' => true,
            'content' => $content,
            'scripts' => [
                'module_js' => $module->getPathUri() . 'views/js/mezistranka_hodnoceni.js'
            ]
        ]));
    }
    
    /**
     * Zpracuje odeslané hodnocení přes AJAX
     */
    private function submitRating()
    {
        $this->sendJSONHeaders();
        
        try {
            // Získání dat z POST requestu
            $rating = (int)Tools::getValue('rating', 0);
            $clickedLink = Tools::getValue('clicked_link', '');
            
            if ($rating < 1 || $rating > 5) {
                throw new Exception('Neplatné hodnocení');
            }
            
            // ID zákazníka a email
            $id_customer = 0;
            $customer_email = null;
            
            // Zjištění informací o zákazníkovi, pokud je přihlášen
            if ($this->context->customer->isLogged()) {
                $id_customer = (int)$this->context->customer->id;
                $customer_email = pSQL($this->context->customer->email);
            }
            
            // IP adresa
            $ip_address = pSQL(Tools::getRemoteAddr());
            
            // Informace o prohlížeči
            $user_agent = pSQL($_SERVER['HTTP_USER_AGENT']);
            
            // Uložení do databáze
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "mezistranka_hodnoceni_stats` 
                    (`rating`, `clicked_link`, `id_customer`, `customer_email`, 
                     `ip_address`, `user_agent`, `date_add`)
                    VALUES 
                    ('$rating', " . ($clickedLink ? "'" . pSQL($clickedLink) . "'" : "NULL") . ", 
                     " . ($id_customer ? $id_customer : "NULL") . ", 
                     " . ($customer_email ? "'$customer_email'" : "NULL") . ", 
                     '$ip_address', '$user_agent', NOW())";
                     
            if (Db::getInstance()->execute($sql)) {
                die(json_encode([
                    'success' => true,
                    'message' => 'Hodnocení bylo úspěšně uloženo'
                ]));
            } else {
                throw new Exception('Chyba při ukládání do databáze');
            }
            
        } catch (Exception $e) {
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }
}