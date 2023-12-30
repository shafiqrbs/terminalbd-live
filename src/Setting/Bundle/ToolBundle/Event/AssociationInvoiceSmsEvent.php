<?php
namespace Setting\Bundle\ToolBundle\Event;
use Appstore\Bundle\BusinessBundle\Entity\BusinessInvoice;
use Symfony\Component\EventDispatcher\Event;


class AssociationInvoiceSmsEvent extends Event
{

    protected $invoice;

    public function __construct(BusinessInvoice $invoice)
    {
        $this->invoice = $invoice;
    }


    /**
     * @return BusinessInvoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }


}