<?php
/**
 * Created by PhpStorm.
 * User: rbs
 * Date: 9/11/16
 * Time: 11:06 PM
 */

namespace Appstore\Bundle\InventoryBundle\Service;

use Symfony\Component\HttpFoundation\Session\Session;

class Cart {

    protected $cart_contents = array();

    /** @var  Session */
    protected $session;

    public function __construct(Session $session){

        $this->session = $session;
        // get the shopping cart array from the session
        $this->cart_contents = $session->get('cart_contents');
        if ($this->cart_contents === NULL){
            // set some base values
            $this->cart_contents = array('cart_subtotal' => 0, 'cart_discount_percent' => 0,'cart_discount' => 0,'cart_vat' => 0, 'cart_total' => 0, 'total_items' => 0);
        }
    }

    /**
     * Cart Contents: Returns the entire cart array
     * @param	bool
     * @return	array
     */
    public function contents(){
        // rearrange the newest first
        $cart = array_reverse($this->cart_contents);

        // remove these so they don't create a problem when showing the cart table
        unset($cart['total_items']);
        unset($cart['cart_total']);
        unset($cart['cart_subtotal']);
        unset($cart['cart_discount']);
        unset($cart['cart_discount_percent']);
        unset($cart['cart_vat']);
        return $cart;
    }

    /**
     * Get cart item: Returns a specific cart item details
     * @param	string	$row_id
     * @return	array
     */
    public function get_item($row_id){
        return (in_array($row_id, array('total_items', 'cart_subtotal', 'cart_discount_percent','cart_discount', 'cart_vat', 'cart_total'), TRUE) OR ! isset($this->cart_contents[$row_id]))
            ? FALSE
            : $this->cart_contents[$row_id];
    }

    /**
     * Get cart item: Returns a specific cart item details
     * @param	integer	$item
     * @return	array
     */
    public function getItem($item){
        return (in_array($item, array('total_items', 'cart_subtotal', 'cart_discount_percent','cart_discount', 'cart_vat', 'cart_total'), TRUE) OR ! isset($this->cart_contents[$item]))
            ? FALSE
            : $this->cart_contents[$item];
    }


    /**
     * Total Items: Returns the total item count
     * @return	int
     */
    public function total_items(){
        return $this->cart_contents['total_items'];
    }

    /**
     * Cart Total: Returns the total price
     * @return	int
     */
    public function subTotal(){
        return $this->cart_contents['cart_subtotal'];
    }

    /**
     * Cart Total: Returns the total price
     * @return	int
     */
    public function discountPercent(){
        return $this->cart_contents['cart_discount_percent'];
    }

    /**
     * Cart Total: Returns the total price
     * @return	int
     */
    public function discount(){
        return $this->cart_contents['cart_discount'];
    }

    /**
     * Cart Total: Returns the total price
     * @return	int
     */
    public function vat(){
        return $this->cart_contents['cart_total'];
    }

    /**
     * Cart Total: Returns the total price
     * @return	int
     */
    public function total(){
        return $this->cart_contents['cart_total'];
    }

    /**
     * Insert items into the cart and save it to the session
     * @param	array
     * @return	bool
     */
    public function insert($item = array()){
        if(!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if(!isset($item['id'], $item['name'], $item['price'], $item['quantity'])){
                return FALSE;
            }else{

                // prep the quantity
                $item['quantity'] = (float) $item['quantity'];
                if($item['quantity'] == 0){
                    return FALSE;
                }
                // prep the price
                $item['price'] = (float) $item['price'];
                // create a unique identifier for the item being inserted into the cart
                $rowid = md5($item['id']);
                // get quantity if it's already there and add it on
                $old_quantity = isset($this->cart_contents[$rowid]['quantity']) ? (int) $this->cart_contents[$rowid]['quantity'] : 0;
                // re-create the entry with unique identifier and updated quantity
                $item['rowid'] = $rowid;
                $item['quantity'] += $old_quantity;
                $this->cart_contents[$rowid] = $item;

                // save Cart Item
                if($this->save_cart()){
                    return isset($rowid) ? $rowid : TRUE;
                }else{
                    return FALSE;
                }
            }
        }
    }

    /**
     * Update the cart
     * @param	array
     * @return	bool
     */
    public function update($item = array()){
        if (!is_array($item) OR count($item) === 0){
            return FALSE;
        }else{
            if (!isset($item['rowid'], $this->cart_contents[$item['rowid']])){
                return FALSE;
            }else{
                // prep the quantity
                if(isset($item['quantity'])){
                    $item['quantity'] = (float) $item['quantity'];
                    // remove the item from the cart, if quantity is zero
                    if ($item['quantity'] == 0){
                        unset($this->cart_contents[$item['rowid']]);
                        return TRUE;
                    }
                }

                // find updatable keys
                $keys = array_intersect(array_keys($this->cart_contents[$item['rowid']]), array_keys($item));
                // prep the price
                if(isset($item['price'])){
                    $item['price'] = (float) $item['price'];
                }
                // product id & name shouldn't be changed
                foreach(array_diff($keys, array('id','name')) as $key){
                    $this->cart_contents[$item['rowid']][$key] = $item[$key];
                }
                // save cart data
                $this->save_cart();
                return TRUE;
            }
        }
    }

    /**
     * Save the cart array to the session
     * @return	bool
     */
    protected function save_cart(){

        $this->cart_contents['total_items'] = $this->cart_contents['cart_subtotal'] = $this->cart_contents['cart_discount_percent'] = $this->cart_contents['cart_discount'] = $this->cart_contents['cart_vat']  = $this->cart_contents['cart_total'] = 0;
        foreach ($this->cart_contents as $key => $val){
            // make sure the array contains the proper indexes
            if(!is_array($val) OR !isset($val['price'], $val['quantity'])){
                continue;
            }

            $this->cart_contents['cart_total'] += ($val['price'] * $val['quantity']);
            $this->cart_contents['total_items'] += $val['quantity'];
            $this->cart_contents[$key]['subtotal'] = ($this->cart_contents[$key]['price'] * $this->cart_contents[$key]['quantity']);
        }

        // if cart empty, delete it from the session
        if(count($this->cart_contents) <= 2){
            $this->session->remove('cart_contents');
            return FALSE;
        }else{
            $this->session->set('cart_contents', $this->cart_contents);
            return TRUE;
        }
    }

    /**
     * Remove Item: Removes an item from the cart
     * @param	int
     * @return	bool
     */
    public function entityUpdate($data){
        // unset & save
        $this->cart_contents[$data['discountPercent']];
        $this->cart_contents[$data['discount']];
        $this->cart_contents[$data['vat']];
        $this->save_cart();
        return TRUE;
    }

    /**
     * Remove Item: Removes an item from the cart
     * @param	int
     * @return	bool
     */
    public function remove($row_id){
        // unset & save
        unset($this->cart_contents[$row_id]);
        $this->save_cart();
        return TRUE;
    }

    /**
     * Destroy the cart: Empties the cart and destroy the session
     * @return	void
     */
    public function destroy(){
        $this->cart_contents = array('cart_total' => 0, 'total_items' => 0);
        $this->session->remove('cart_contents');
    }
}