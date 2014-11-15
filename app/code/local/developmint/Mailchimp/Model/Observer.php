<?php

/**
 * Class Developmint_Mailchimp_Model_Observer
 */
class Developmint_Mailchimp_Model_Observer
{
    /**
     * @param $observer
     * Observer function attached to the sales_order_place_after event. Subscribes the
     * customer to the mailchimp if they selected the newsletter-signup option
     */
    public function subscribe($observer)
    {
        //grab the request
        $request = Mage::app()->getFrontController()->getRequest();

        /* newsletter-signup needs to be defined as a value on the checkout form */
        if ($request->getPost('newsletter-signup', '')) {
            //retrieve the api key and list id from the config
            $apiKey = Mage::getStoreConfig('developmint/developmint_group/developmint_mailchimp_api_key', Mage::app()->getStore());
            $listId = Mage::getStoreConfig('developmint/developmint_group/developmint_mailchimp_list_id', Mage::app()->getStore());

            //extract the order object that was passed from the event
            $order = $observer->getEvent()->getOrder();

            if ($apiKey && $listId) {
                $api = new Developmint_Mailchimp_Model_MCAPI($apiKey);

                $merge_vars = array(
                    'FNAME' => $order->getCustomerFirstname(),
                    'LNAME' => $order->getCustomerLastname(),
                    'INTERESTS' => ''
                );

                $result = $api->listSubscribe($listId, $order->getCustomerEmail(), $merge_vars, 'html', false);

                if ($api->errorCode) {
                    Mage::log('Failed to add e-mail to mailchimp list. ' . $api->errorMessage);
                }
            }
        }
    }
}
