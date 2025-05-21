<?php
/**
 * Skript pro sběr analytických dat do databáze PrestaShop
 * Umístěte do: /modules/mezistranka_hodnoceni/track.php
 */

// Načtení PrestaShop prostředí
include_once(dirname(__FILE__) . '/../../config/config.inc.php');
include_once(dirname(__FILE__) . '/../../init.php');

// Headery pro CORS a JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Zpracování odpovědi
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
    if (Context::getContext()->customer->isLogged()) {
        $id_customer = (int)Context::getContext()->customer->id;
        $customer_email = pSQL(Context::getContext()->customer->email);
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
            echo json_encode(['success' => true, 'message' => 'Hodnocení uloženo']);
            exit;
        } else {
            throw new Exception('Chyba při ukládání do databáze');
        }
    } else {
        throw new Exception('Neznámý typ události');
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}