<?php
/**
 * Controller pro sledování konverzí
 */

class Mezistranka_HodnoceniTrackModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        header('Content-Type: application/json');
        
        try {
            // Získání dat z POST requestu
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!$data || !isset($data['event'])) {
                throw new Exception('Neplatná data');
            }
            
            // ID zákazníka a email
            $id_customer = 0;
            $customer_email = null;
            
            // Zjištění informací o zákazníkovi, pokud je přihlášen
            if ($this->context->customer->isLogged()) {
                $id_customer = (int)$this->context->customer->id;
                $customer_email = pSQL($this->context->customer->email);
            }
            
            // Zpracování různých typů událostí
            if ($data['event'] === 'rating_conversion') {
                $rating = isset($data['data']['value']) ? (int)$data['data']['value'] : 0;
                $clicked_link = isset($data['data']['label']) ? pSQL($data['data']['label']) : '';
                
                // IP adresa
                $ip_address = pSQL(Tools::getRemoteAddr());
                
                // Informace o prohlížeči
                $user_agent = pSQL($_SERVER['HTTP_USER_AGENT']);
                
                // Uložení do databáze - vždy zaznamenat každé hodnocení
                $sql = "INSERT INTO `" . _DB_PREFIX_ . "mezistranka_hodnoceni_stats` 
                        (`rating`, `clicked_link`, `id_customer`, `customer_email`, 
                         `ip_address`, `user_agent`, `date_add`)
                        VALUES 
                        ('$rating', " . ($clicked_link ? "'$clicked_link'" : "NULL") . ", 
                         " . ($id_customer ? $id_customer : "NULL") . ", 
                         " . ($customer_email ? "'$customer_email'" : "NULL") . ", 
                         '$ip_address', '$user_agent', NOW())";
                         
                if (Db::getInstance()->execute($sql)) {
                    die(json_encode(['success' => true, 'message' => 'Hodnocení uloženo']));
                } else {
                    throw new Exception('Chyba při ukládání do databáze');
                }
            } else {
                throw new Exception('Neznámý typ události');
            }
            
        } catch (Exception $e) {
            die(json_encode(['success' => false, 'error' => $e->getMessage()]));
        }
    }
}