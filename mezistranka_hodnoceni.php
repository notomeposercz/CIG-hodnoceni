<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Mezistranka_Hodnoceni extends Module
{
    public function __construct()
    {
        $this->name = 'mezistranka_hodnoceni';
        $this->tab = 'front_office_features';
        $this->version = '1.0.1';
        $this->author = 'ChatGPT';
        $this->need_instance = 0;
        $this->bootstrap = true;
        $this->ps_versions_compliancy = ['min' => '1.7', 'max' => '8.99.99'];

        parent::__construct();

        $this->displayName = $this->l('Mezistranka hodnoceni');
        $this->description = $this->l('Vlastni mezistranka pro filtrovani hodnoceni zakazniku.');
    }

    public function install()
    {
        // Vytvoření tabulky pro statistiky hodnocení
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "mezistranka_hodnoceni_stats` (
            `id_stat` int(11) NOT NULL AUTO_INCREMENT,
            `rating` int(1) NOT NULL,
            `clicked_link` varchar(255) DEFAULT NULL,
            `id_customer` int(11) DEFAULT NULL,
            `customer_email` varchar(255) DEFAULT NULL,
            `ip_address` varchar(45) DEFAULT NULL,
            `user_agent` varchar(255) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_stat`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }

        // V případě, že se instaluje poprvé, aktualizujeme všechny existující CMS stránky
        $this->updateAllCmsWithShortcode();

        return parent::install() && 
               $this->registerHook('displayHeader') &&
               $this->registerHook('actionFrontControllerSetMedia') &&
               $this->registerHook('moduleRoutes') &&
               $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('actionAdminControllerSetMedia') &&
               $this->registerHook('actionCmsObjectSave') && 
               $this->registerHook('actionObjectCmsUpdateAfter') &&
               $this->registerHook('displayFooter') &&
               // Nový hook pro filtrování obsahu CMS stránek
               $this->registerHook('filterCmsContent');
    }

    /**
     * Hook volaný po každém uložení CMS stránky v administraci
     */
    public function hookActionCmsObjectSave($params)
    {
        return $this->processShortcodeInCms($params);
    }
    
    /**
     * Hook volaný po každé aktualizaci CMS stránky
     */
    public function hookActionObjectCmsUpdateAfter($params)
    {
        return $this->processShortcodeInCms($params);
    }
    
    /**
     * Zpracuje shortcode v CMS stránce a uloží upravenou verzi do databáze
     */
    private function processShortcodeInCms($params)
    {
        // Získání HTML obsahu formuláře hodnocení
        $ratingHtml = $this->getRatingContent();
        $ratingHtml = addslashes($ratingHtml); // Escapování pro SQL
        
        // Aktualizace všech CMS stránek, které obsahují shortcode
        $sql = "UPDATE `" . _DB_PREFIX_ . "cms_lang` 
                SET `content` = REPLACE(`content`, '{mezistrankahodnoceni}', '" . $ratingHtml . "')
                WHERE `content` LIKE '%{mezistrankahodnoceni}%'";
        
        Db::getInstance()->execute($sql);
        
        // Vyčištění cache
        if (method_exists('Tools', 'clearAllCache')) {
            Tools::clearAllCache();
        }
        
        return true;
    }

    /**
     * Aktualizace všech CMS stránek se shortcodem
     */
    public function updateAllCmsWithShortcode()
    {
        // Získání HTML obsahu formuláře hodnocení
        $ratingHtml = $this->getRatingContent();
        $ratingHtml = addslashes($ratingHtml); // Escapování pro SQL
        
        // Aktualizace všech CMS stránek, které obsahují shortcode
        $sql = "UPDATE `" . _DB_PREFIX_ . "cms_lang` 
                SET `content` = REPLACE(`content`, '{mezistrankahodnoceni}', '" . $ratingHtml . "')
                WHERE `content` LIKE '%{mezistrankahodnoceni}%'";
        
        $result = Db::getInstance()->execute($sql);
        
        // Vyčištění cache
        if (method_exists('Tools', 'clearAllCache')) {
            Tools::clearAllCache();
        }
        
        return $result;
    }

    /**
     * Získá obsah hodnocení
     */
    private function getRatingContent()
    {
        // Načtení formuláře hodnocení
        $this->context->smarty->assign([
            'page_title' => $this->l('Ohodnoťte nás'),
            'module_dir' => $this->_path,
        ]);
        
        // Získání HTML obsahu formuláře hodnocení
        return $this->fetch('module:' . $this->name . '/views/templates/front/rating_content.tpl');
    }

    /**
     * Nový hook pro filtrování obsahu CMS stránek - doplňuje dynamické nahrazení
     */
    public function hookFilterCmsContent($params)
    {
        if (isset($params['object']) && $params['object'] instanceof CMSCore) {
            $content = $params['object']->content;
            
            // Pokud obsahuje shortcode, nahradíme ho
            if (strpos($content, '{mezistrankahodnoceni}') !== false) {
                $ratingHtml = $this->getRatingContent();
                $content = str_replace('{mezistrankahodnoceni}', $ratingHtml, $content);
                $params['object']->content = $content;
            }
        }
        
        return $params;
    }

    public function hookDisplayHeader()
    {
        // Vždy načíst CSS a JS pro hodnocení (podmínku lze přidat později)
        $this->context->controller->registerStylesheet(
            'module-mezistranka-css',
            'modules/'.$this->name.'/views/css/mezistranka_hodnoceni.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'module-mezistranka-js',
            'modules/'.$this->name.'/views/js/mezistranka_hodnoceni.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }
    
    public function hookModuleRoutes()
    {
        return [
            'module-mezistranka_hodnoceni-rating' => [
                'controller' => 'rating',
                'rule' => 'ohodnoceni',
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'mezistranka_hodnoceni',
                    'controller' => 'rating',
                ]
            ]
        ];
    }

    public function hookActionFrontControllerSetMedia()
    {
        // Vždy načíst CSS a JS pro hodnocení (podmínku lze přidat později)
        $this->context->controller->registerStylesheet(
            'module-mezistranka-css',
            'modules/'.$this->name.'/views/css/mezistranka_hodnoceni.css',
            ['media' => 'all', 'priority' => 150]
        );
        $this->context->controller->registerJavascript(
            'module-mezistranka-js',
            'modules/'.$this->name.'/views/js/mezistranka_hodnoceni.js',
            ['position' => 'bottom', 'priority' => 150]
        );
    }

    /**
     * Hook pro admin CSS a JS
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') === $this->name) {
            $adminCssUri = __PS_BASE_URI__ . 'modules/' . $this->name . '/views/css/admin.css';
            $this->context->controller->addCSS($adminCssUri);
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        }
    }

    public function hookActionAdminControllerSetMedia()
    {
        if (Tools::getValue('configure') === $this->name) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        }
    }
    
    /**
     * Hook pro footer - záložní řešení pro případ, že by hooky pro CMS nefungovaly
     * Vylepšená verze s dynamickým nahrazením
     */
    public function hookDisplayFooter()
    {
        // Nejprve ověříme, zda jsme na CMS stránce
        if ($this->context->controller instanceof CmsController) {
            $cms_id = (int)Tools::getValue('id_cms');
            
            if ($cms_id > 0) {
                // Získáme obsah aktuální stránky
                $sql = "SELECT `content` FROM `" . _DB_PREFIX_ . "cms_lang` 
                        WHERE `id_cms` = " . $cms_id . "
                        AND `id_lang` = " . (int)$this->context->language->id;
                
                $content = Db::getInstance()->getValue($sql);
                
                // Pokud stránka obsahuje shortcode, vložíme JS pro nahrazení
                if (strpos($content, '{mezistrankahodnoceni}') !== false) {
                    // Načtení formuláře hodnocení
                    $ratingHtml = $this->getRatingContent();
                    // Escapování pro JavaScript
                    $ratingHtml = str_replace(["'", "\r", "\n"], ["\\'", "", ""], $ratingHtml);
                    
                    return '<script>
                        document.addEventListener("DOMContentLoaded", function() {
                            // Najděte všechny výskyty textu {mezistrankahodnoceni} v obsahu
                            var elements = document.querySelectorAll(".cms-content, .page-content, #cms, #content");
                            
                            elements.forEach(function(element) {
                                if (element && element.innerHTML && element.innerHTML.includes("{mezistrankahodnoceni}")) {
                                    // Nahradit shortcode obsahem komponenty
                                    element.innerHTML = element.innerHTML.replace(/{mezistrankahodnoceni}/g, \'' . $ratingHtml . '\');
                                    
                                    // Načtení skriptů pro inicializaci
                                    var script = document.createElement("script");
                                    script.textContent = "initializeRatingModule();";
                                    document.body.appendChild(script);
                                }
                            });
                        });
                    </script>';
                }
            }
        }
        
        return '';
    }

    /**
     * Zobrazení obsahu administrace modulu
     */
    public function getContent()
    {
        $output = '';
        
        // Pokud bylo posláno tlačítko pro aktualizaci všech stránek s shortcodem
        if (Tools::isSubmit('update_shortcodes')) {
            if ($this->updateAllCmsWithShortcode()) {
                $output .= $this->displayConfirmation($this->l('Všechny stránky se shortcodem byly aktualizovány.'));
            } else {
                $output .= $this->displayError($this->l('Nastala chyba při aktualizaci stránek.'));
            }
        }
        
        // Aktualizace tlačítko pro manuální spuštění
        $update_shortcode_btn = $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&update_shortcodes=1';
        $this->context->smarty->assign('update_shortcode_btn', $update_shortcode_btn);
        
        // Načtení CSS přímo do proměnné
        $adminCss = '';
        $cssFile = dirname(__FILE__) . '/views/css/admin.css';
        if (file_exists($cssFile)) {
            $adminCss = file_get_contents($cssFile);
        }
        
        // Přiřazení CSS do šablony
        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'current_tab' => Tools::isSubmit('statistics_tab') ? 'statistics' : 'configuration',
            'statistics' => $this->getStatistics(),
            'ps_version' => _PS_VERSION_,
            'export_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&export_stats=1',
            'config_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
            'statistics_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&statistics_tab=1',
            'admin_css' => $adminCss, // Přidáme CSS jako proměnnou
        ]);
        
        // Zpracování exportu statistik
        if (Tools::isSubmit('export_stats')) {
            $this->exportStatisticsCSV();
        }
        
        // Zjištění aktuální záložky
        $current_tab = 'configuration';
        if (Tools::isSubmit('statistics_tab')) {
            $current_tab = 'statistics';
        }
        
        // Zpracování statistik
        $statistics = [];
        $statSummary = [];
        
        if ($current_tab == 'statistics') {
            // Načtení statistik
            $statistics = $this->getStatistics();
            
            // Příprava souhrnných statistik
            $total_ratings = count($statistics);
            $sum_ratings = 0;
            $positive_count = 0;
            $negative_count = 0;
            $rating_distribution = [
                1 => ['count' => 0, 'percent' => 0],
                2 => ['count' => 0, 'percent' => 0],
                3 => ['count' => 0, 'percent' => 0],
                4 => ['count' => 0, 'percent' => 0],
                5 => ['count' => 0, 'percent' => 0],
            ];
            $link_clicks = [];
            
            // Zpracování dat
            foreach ($statistics as $stat) {
                $rating = (int)$stat['rating'];
                $sum_ratings += $rating;
                
                // Počítání pozitivních a negativních hodnocení
                if ($rating >= 4) {
                    $positive_count++;
                } else {
                    $negative_count++;
                }
                
                // Distribuce hodnocení
                if (isset($rating_distribution[$rating])) {
                    $rating_distribution[$rating]['count']++;
                }
                
                // Počítání kliknutí na odkazy
                if (!empty($stat['clicked_link'])) {
                    $link = $stat['clicked_link'];
                    if (!isset($link_clicks[$link])) {
                        $link_clicks[$link] = ['count' => 0, 'percent' => 0];
                    }
                    $link_clicks[$link]['count']++;
                }
            }
            
            // Výpočet procent pro distribuci hodnocení
            if ($total_ratings > 0) {
                foreach ($rating_distribution as $rating => &$data) {
                    $data['percent'] = ($data['count'] / $total_ratings) * 100;
                }
                
                // Procenta pro pozitivní a negativní hodnocení
                $positive_percent = ($positive_count / $total_ratings) * 100;
                $negative_percent = ($negative_count / $total_ratings) * 100;
                
                // Procenta pro konverze na odkazy
                foreach ($link_clicks as $link => &$data) {
                    $data['percent'] = ($data['count'] / $total_ratings) * 100;
                }
            } else {
                $positive_percent = 0;
                $negative_percent = 0;
            }
            
            // Průměrné hodnocení
            $average_rating = $total_ratings > 0 ? $sum_ratings / $total_ratings : 0;
            
            // Příprava souhrnných statistik pro šablonu
            $statSummary = [
                'total_ratings' => $total_ratings,
                'average_rating' => $average_rating,
                'positive_count' => $positive_count,
                'positive_percent' => $positive_percent,
                'negative_count' => $negative_count,
                'negative_percent' => $negative_percent,
                'rating_distribution' => $rating_distribution,
                'link_clicks' => $link_clicks,
            ];
        }
        
        // Přiřazení proměnných do šablony
        $this->context->smarty->assign([
            'module_dir' => $this->_path,
            'current_tab' => $current_tab,
            'statistics' => $statistics,
            'ps_version' => _PS_VERSION_,
            'export_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&export_stats=1',
            'config_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name,
            'statistics_url' => $this->context->link->getAdminLink('AdminModules') . '&configure=' . $this->name . '&statistics_tab=1',
        ]);
        
        // Přidání statistických dat, pokud jsou k dispozici
        if (!empty($statSummary)) {
            $this->context->smarty->assign($statSummary);
        }

        // Zobrazení šablony
        return $output . $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    /**
     * Získá statistiky hodnocení z databáze
     */
    private function getStatistics()
    {
        $sql = "SELECT s.*, COALESCE(c.firstname, '') as firstname, COALESCE(c.lastname, '') as lastname
                FROM `" . _DB_PREFIX_ . "mezistranka_hodnoceni_stats` s
                LEFT JOIN `" . _DB_PREFIX_ . "customer` c ON (s.id_customer = c.id_customer)
                ORDER BY s.date_add DESC";
        
        return Db::getInstance()->executeS($sql);
    }

    /**
     * Exportuje statistiky hodnocení do CSV souboru
     */
    public function exportStatisticsCSV()
    {
        $stats = $this->getStatistics();
        
        if (empty($stats)) {
            $this->context->controller->errors[] = $this->l('Nejsou k dispozici žádné statistiky pro export.');
            return false;
        }
        
        $filename = 'statistiky-hodnoceni-' . date('Y-m-d-H-i-s') . '.csv';
        
        // Hlavičky pro CSV
        $headers = [
            $this->l('ID'),
            $this->l('Hodnocení (1-5)'),
            $this->l('Odkaz'),
            $this->l('ID zákazníka'),
            $this->l('Email zákazníka'),
            $this->l('IP adresa'),
            $this->l('Prohlížeč'),
            $this->l('Datum a čas')
        ];
        
        // Příprava dat
        $data = [];
        foreach ($stats as $stat) {
            $data[] = [
                $stat['id_stat'],
                $stat['rating'],
                $stat['clicked_link'] ? $stat['clicked_link'] : $this->l('Žádný'),
                $stat['id_customer'] ? $stat['id_customer'] : $this->l('Nepřihlášený'),
                $stat['customer_email'] ? $stat['customer_email'] : '-',
                $stat['ip_address'],
                $stat['user_agent'],
                $stat['date_add']
            ];
        }
        
        // Nastavení hlaviček pro stahování
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        // Otevření výstupního streamu
        $output = fopen('php://output', 'w');
        
        // Výpis BOM pro správné zobrazení diakritiky v Excelu
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Výpis hlaviček
        fputcsv($output, $headers, ';');
        
        // Výpis dat
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit;
    }

    public function uninstall()
    {
        // Odstranění tabulky statistik při odinstalaci
        // Poznámka: Můžete toto zakomentovat, pokud chcete zachovat data i po odinstalaci
        $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "mezistranka_hodnoceni_stats`";
        Db::getInstance()->execute($sql);
        
        return parent::uninstall();
    }
}