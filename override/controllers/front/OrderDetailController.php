<?php
/**
 * ZomaPro - Override du détail de commande : récapitulatif HT/TTC, TVA, facture,
 * pour l'affichage façon maquette.
 */
class OrderDetailController extends OrderDetailControllerCore
{
    public function initContent()
    {
        parent::initContent();

        $idOrder = (int) Tools::getValue('id_order');
        if (!$idOrder && ($ref = Tools::getValue('reference'))) {
            $col = Order::getByReference($ref);
            if ($col && count($col)) {
                $first = $col->getFirst();
                $idOrder = (int) $first->id;
            }
        }

        if ($idOrder) {
            $order = new Order($idOrder);
            if (Validate::isLoadedObject($order) && $order->id_customer == $this->context->customer->id) {
                $tva = $order->total_paid_tax_incl - $order->total_paid_tax_excl;
                $zoma = [
                    'sous_total_ht' => Tools::displayPrice($order->total_products),
                    'remise' => Tools::displayPrice($order->total_discounts_tax_excl),
                    'has_remise' => ($order->total_discounts_tax_excl > 0),
                    'shipping_val' => (float) $order->total_shipping_tax_excl,
                    'shipping' => Tools::displayPrice($order->total_shipping_tax_excl),
                    'total_ht' => Tools::displayPrice($order->total_paid_tax_excl),
                    'tva' => Tools::displayPrice($tva),
                    'tva_rate' => ($order->total_paid_tax_excl > 0) ? (int) round((($order->total_paid_tax_incl / $order->total_paid_tax_excl) - 1) * 100) : 0,
                    'total_ttc' => Tools::displayPrice($order->total_paid_tax_incl),
                    'paid' => (bool) $order->hasBeenPaid(),
                    'invoice_number' => '',
                    'invoice_date' => '',
                ];

                $invoices = $order->getInvoicesCollection();
                if ($invoices && count($invoices)) {
                    $inv = $invoices->getFirst();
                    if (Validate::isLoadedObject($inv)) {
                        $zoma['invoice_number'] = $inv->getInvoiceNumberFormatted($this->context->language->id, $order->id_shop);
                        $zoma['invoice_date'] = Tools::displayDate($inv->date_add);
                    }
                }

                $this->context->smarty->assign('zoma_od', $zoma);
            }
        }

        $this->setTemplate('customer/order-detail');
    }
}
