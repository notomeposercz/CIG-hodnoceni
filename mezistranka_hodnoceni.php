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
        $this->version = '1.0.0';
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

        return parent::install() && 
               $this->registerHook('displayHeader') &&
               $this->registerHook('actionFrontControllerSetMedia') &&
               $this->registerHook('moduleRoutes') &&
               $this->registerHook('displayBackOfficeHeader') &&
               $this->registerHook('actionAdminControllerSetMedia');
    }

    public function hookDisplayHeader()
    {
        // Modernější zápis pro PrestaShop 8.x
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
        // Pro moderní témata v PS 8.x
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
        $adminCssUri = __PS_BASE_URI__ . 'modules/' . $this->name . '/views/css/admin.css?v=' . time();
        $this->context->controller->addCSS($adminCssUri);
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        
        // Pro kontrolu přidejte log
        PrestaShopLogger::addLog('Admin CSS URI: ' . $adminCssUri, 1);
    }
}

public function hookActionAdminControllerSetMedia()
{
    if (Tools::getValue('configure') === $this->name) {
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
    }
}

    /**
     * Zobrazení obsahu administrace modulu
     */
    public function getContent()
    {
        $output = '';
        
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
