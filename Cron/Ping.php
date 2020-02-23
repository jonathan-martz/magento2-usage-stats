<?php

namespace JonathanMartz\SupportForm\Cron;

use Exception;
use JonathanMartz\SupportForm\Model\RequestFactory;
use JonathanMartz\SupportForm\Model\ResourceModel\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;


/**
 * Class Clear
 * @package JonathanMartz\SupportForm\Cron
 */
class Clear
{
    /**
     * @var CollectionFactory
     */
    private $supportrequest;
    /**
     * @var RequestFactory
     */
    private $requestFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;


    /**
     * Clear constructor.
     * @param CollectionFactory $supportrequest
     * @param RequestFactory $requestFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CollectionFactory $supportrequest,
        RequestFactory $requestFactory,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->supportrequest = $supportrequest;
        $this->requestFactory = $requestFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     *
     */
    public function execute()
    {
        $collection = $this->supportrequest->create();
        $collection->addFieldToFilter('customer_id', ['neq' => null])->setPageSize(10)->setCurPage(1);
        $requests = $collection->getItems();

        if(count($requests) > 0) {
            foreach($requests as $id => $model) {

                try {
                    $this->customerRepository->get($model->getData('email'), 1);
                }
                catch(Exception $e) {
                    $model->delete();
                }
            }
        }
    }
}
