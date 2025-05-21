<?php
/**
 * Controller pro zobrazení stránky hodnocení
 */

class Mezistranka_HodnoceniRatingModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        
        // Načtení konfigurace
        $this->context->smarty->assign([
            'page_title' => $this->trans('Ohodnoťte nás', [], 'Modules.Mezistranka_hodnoceni.Rating'),
            'hodnoceni_title' => Configuration::get('MEZISTRANKAHODNOCENI_TITLE'),
            'hodnoceni_positive_title' => Configuration::get('MEZISTRANKAHODNOCENI_POSITIVE_TITLE'),
            'hodnoceni_positive_text' => Configuration::get('MEZISTRANKAHODNOCENI_POSITIVE_TEXT'),
            'hodnoceni_negative_title' => Configuration::get('MEZISTRANKAHODNOCENI_NEGATIVE_TITLE'),
            'hodnoceni_negative_text' => Configuration::get('MEZISTRANKAHODNOCENI_NEGATIVE_TEXT'),
            'hodnoceni_link_google' => Configuration::get('MEZISTRANKAHODNOCENI_LINK_GOOGLE'),
            'hodnoceni_link_seznam' => Configuration::get('MEZISTRANKAHODNOCENI_LINK_SEZNAM'),
            'hodnoceni_link_heureka' => Configuration::get('MEZISTRANKAHODNOCENI_LINK_HEUREKA'),
            'hodnoceni_phone' => Configuration::get('MEZISTRANKAHODNOCENI_PHONE'),
            'hodnoceni_email' => Configuration::get('MEZISTRANKAHODNOCENI_EMAIL'),
        ]);
        
        $this->setTemplate('module:mezistranka_hodnoceni/views/templates/front/rating.tpl');
    }
}