<?php
/**
 * Created by PhpStorm.
 * User: dhaka
 * Date: 8/19/14
 * Time: 5:24 PM
 */

namespace Setting\Bundle\ToolBundle\Event;

use Appstore\Bundle\DomainUserBundle\Entity\Customer;
use Symfony\Component\EventDispatcher\Event;


class CustomerOutstandingSmsEvent extends Event
{

	/** @var Customer  */
	protected $customer;


	public function __construct(Customer $customer)
	{
		$this->customer = $customer;
	}

	/**
	 * @return Customer
	 */
	public function getCustomer()
	{
		return $this->customer;
	}


}