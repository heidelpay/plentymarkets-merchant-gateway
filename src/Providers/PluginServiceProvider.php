<?php

namespace HeidelpayMGW\Providers;

use HeidelpayMGW\Helpers\Loggable;
use Plenty\Plugin\ServiceProvider;
use Plenty\Plugin\Events\Dispatcher;
use Plenty\Modules\Order\Models\Order;
use HeidelpayMGW\Helpers\PaymentHelper;
use HeidelpayMGW\Helpers\SessionHelper;
use Plenty\Plugin\Translation\Translator;
use Plenty\Modules\Order\Models\OrderType;
use HeidelpayMGW\Models\PaymentInformation;
use HeidelpayMGW\Methods\CardsPaymentMethod;
use HeidelpayMGW\Methods\IdealPaymentMethod;
use Plenty\Modules\Document\Models\Document;
use HeidelpayMGW\Methods\PaypalPaymentMethod;
use HeidelpayMGW\Methods\SofortPaymentMethod;
use HeidelpayMGW\Methods\InvoicePaymentMethod;
use HeidelpayMGW\Configuration\PluginConfiguration;
use HeidelpayMGW\Methods\FlexiPayDirectPaymentMethod;
use HeidelpayMGW\Methods\SepaDirectDebitPaymentMethod;
use HeidelpayMGW\Providers\PluginRouteServiceProvider;
use Plenty\Modules\Order\Pdf\Models\OrderPdfGeneration;
use HeidelpayMGW\Methods\InvoiceGuaranteedPaymentMethod;
use Plenty\Modules\Basket\Events\Basket\AfterBasketCreate;
use Plenty\Modules\Payment\Events\Checkout\ExecutePayment;
use HeidelpayMGW\Methods\InvoiceGuaranteedPaymentMethodB2b;
use HeidelpayMGW\Repositories\PaymentInformationRepository;
use Plenty\Modules\Frontend\Events\FrontendLanguageChanged;
use HeidelpayMGW\EventProcedures\RefundTransactionProcedure;
use Plenty\Modules\Order\Pdf\Events\OrderPdfGenerationEvent;
use HeidelpayMGW\EventProcedures\AuthorizationChargeProcedure;
use HeidelpayMGW\EventProcedures\FinalizeTransactionProcedure;
use Plenty\Modules\Basket\Events\BasketItem\AfterBasketItemAdd;
use HeidelpayMGW\Methods\SepaDirectDebitGuaranteedPaymentMethod;
use Plenty\Modules\EventProcedures\Services\Entries\ProcedureEntry;
use Plenty\Modules\EventProcedures\Services\EventProceduresService;
use Plenty\Modules\Payment\Events\Checkout\GetPaymentMethodContent;
use Plenty\Modules\Payment\Method\Contracts\PaymentMethodContainer;

/**
 * Service provider of plugin
 *
 * Copyright (C) 2020 heidelpay GmbH
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * @link https://docs.heidelpay.com/
 *
 * @package  heidelpayMGW/providers
 *
 * @author Rimantas <development@heidelpay.com>
 */
class PluginServiceProvider extends ServiceProvider
{
    use Loggable;

    /**
     * Register service providers
     */
    public function register()
    {
        $this->getApplication()->register(PluginRouteServiceProvider::class);
    }

    /**
     * Everything that needs constant attention goes here. Like system events and so on.
     *
     * @param PaymentHelper $paymentHelper  Helper class to handle payment data
     * @param PaymentMethodContainer $payContainer  Plentymarkets PaymentMethodContainer
     * @param SessionHelper $sessionHelper  Helper class to save information to session
     * @param Dispatcher $eventDispatcher  Plentymarkets event Dispatcher
     * @param PaymentInformationRepository $paymentInformationRepository  heidelpay payment information repository
     * @param EventProceduresService $eventProceduresService
     *
     * @return void
     */
    public function boot(
        PaymentHelper $paymentHelper,
        PaymentMethodContainer $payContainer,
        SessionHelper $sessionHelper,
        Dispatcher $eventDispatcher,
        PaymentInformationRepository $paymentInformationRepository,
        EventProceduresService $eventProceduresService
    ) {
        // Listen for the language changed event
        $eventDispatcher->listen(
            FrontendLanguageChanged::class,
            function (FrontendLanguageChanged $event) use ($sessionHelper) {
                $sessionHelper->setValue('frontendLocale', $event->getLanguage());
            }
        );
        
        // Invoice
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_INVOICE,
            InvoicePaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // Invoice guaranteed B2C
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_INVOICE_GUARANTEED,
            InvoiceGuaranteedPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // Invoice guaranteed B2B
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_INVOICE_GUARANTEED_B2B,
            InvoiceGuaranteedPaymentMethodB2b::class,
            $this->paymentMethodEvents()
        );
        // Credit/Debit card
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_CARDS,
            CardsPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // SEPA Direct Debit
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_SEPA_DIRECT_DEBIT,
            SepaDirectDebitPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // SEPA Direct Debit Guaranteed
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_SEPA_DIRECT_DEBIT_GUARANTEED,
            SepaDirectDebitGuaranteedPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // PayPal
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_PAYPAL,
            PaypalPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // iDEAL
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_IDEAL,
            IdealPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // Sofort
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_SOFORT,
            SofortPaymentMethod::class,
            $this->paymentMethodEvents()
        );
        // FlexiPay Direct
        $payContainer->register(
            PluginConfiguration::PLUGIN_KEY.'::'.PluginConfiguration::PAYMENT_KEY_FLEXIPAY_DIRECT,
            FlexiPayDirectPaymentMethod::class,
            $this->paymentMethodEvents()
        );

        // charge authorization event
        $eventProceduresService->registerProcedure(
            'authorizationCharge',
            ProcedureEntry::EVENT_TYPE_ORDER,
            [
                'de' => 'Autorisierung erfassen ('.PluginConfiguration::PLUGIN_NAME.')',
                'en' => 'Authorization charge ('.PluginConfiguration::PLUGIN_NAME.')'
            ],
            AuthorizationChargeProcedure::class . '@handle'
        );
        //perform finalize transaction
        $eventProceduresService->registerProcedure(
            'finalizeTransaction',
            ProcedureEntry::EVENT_TYPE_ORDER,
            [
                'de' => 'Finalize Transaktion ('.PluginConfiguration::PLUGIN_NAME.')',
                'en' => 'Finalize transaction ('.PluginConfiguration::PLUGIN_NAME.')'
            ],
            FinalizeTransactionProcedure::class . '@handle'
        );
        //perform refund transaction
        $eventProceduresService->registerProcedure(
            'cancelTransaction',
            ProcedureEntry::EVENT_TYPE_ORDER,
            [
                'de' => 'Cancel Transaktion ('.PluginConfiguration::PLUGIN_NAME.')',
                'en' => 'Cancel transaction ('.PluginConfiguration::PLUGIN_NAME.')'
            ],
            RefundTransactionProcedure::class . '@handle'
        );

        $logger = $this->getLogger(__METHOD__);
        //Listen for the event that gets the payment method content before Order creation
        $eventDispatcher->listen(
            GetPaymentMethodContent::class,
            function (GetPaymentMethodContent $event) use ($sessionHelper, $paymentHelper, $logger) {
                try {
                    //skip not HeidelpayMGW payment
                    if (!$paymentHelper->isHeidelpayMGWMOP($event->getMop())) {
                        return;
                    }
                    /** @var array $paymentResource */
                    $paymentResource = $sessionHelper->getValue('paymentResource');
                    if (!empty($paymentResource)) {
                        //make a charge
                        /** @var array $response */
                        $response = $paymentHelper->executeCharge($paymentResource, $event->getMop());
                        
                        $event->setValue($response['value']);
                        return $event->setType($response['type']);
                    }
                } catch (\Exception $e) {
                    $logger->exception(
                        'translation.exception',
                        [
                            'error' => $e->getMessage()
                        ]
                    );
                }

                $logger->exception(
                    'translation.noPaymentResource',
                    [
                        'methodOfPayment' => $event->getMop(),
                        'paymentResource' => $sessionHelper->getValue('paymentResource')
                    ]
                );
                
                /** @var Translator $translator */
                $translator = pluginApp(Translator::class);

                $event->setValue($translator->trans(PluginConfiguration::PLUGIN_NAME.'::translation.unexpectedError'));
                return $event->setType(GetPaymentMethodContent::RETURN_TYPE_ERROR);
            }
        );

        //Listen for the event that executes the payment after Order creat
        $eventDispatcher->listen(
            ExecutePayment::class,
            function (ExecutePayment $event) use ($paymentHelper, $sessionHelper, $paymentInformationRepository, $logger) {
                try {
                    //if payment method not ours, we don't care
                    if (!$paymentHelper->isHeidelpayMGWMOP($event->getMop())) {
                        return;
                    }
                    /** @var array $paymentInformation */
                    $paymentInformation = $sessionHelper->getValue('paymentInformation');
                    if (!empty($paymentInformation)) {
                        $paymentInformationRepository->updateOrderId($paymentInformation['paymentType'], (string)$event->getOrderId());
                        /** @var mixed $pluginPaymentService */
                        $pluginPaymentService = $paymentHelper->getPluginPaymentService($event->getOrderId());
                        $pluginPaymentService->addExternalOrderId($event->getOrderId(), $sessionHelper->getValue('externalOrderId'));
                        // use paymentResource from RedirectController@processRedirect
                        $paymentHelper->handleWebhook($sessionHelper->getValue('paymentResource'));
                    }
                } catch (\Exception $e) {
                    $logger->exception(
                        'translation.exception',
                        [
                            'error' => $e->getMessage()
                        ]
                    );

                    $event->setValue('Unexpected error.');
                    return $event->setType('error');
                }
            }
        );

        //Handle document generation
        $eventDispatcher->listen(
            OrderPdfGenerationEvent::class,
            static function (OrderPdfGenerationEvent $event) use ($paymentHelper, $paymentInformationRepository, $logger) {
                try {
                    /** @var Order $order */
                    $order = $event->getOrder();
                    /** @var string $docType */
                    $docType = $event->getDocType();
                    /** @var string $mopId */
                    $mopId = $order->methodOfPaymentId;
                    if (!$paymentHelper->isHeidelpayMGWMOP((int)$mopId)) {
                        return;
                    }

                    //get sales Order ID; when generating return note sales Order will be parentOrder
                    $orderId = $order->typeId === OrderType::TYPE_RETURN ? $order->parentOrder->id : $order->id;
                    /** @var PaymentInformation $paymentInformation */
                    $paymentInformation = $paymentInformationRepository->getByOrderId($orderId);
                    if (empty($paymentInformation)) {
                        return;
                    }
                    switch ($docType) {
                        case Document::INVOICE:
                            //add additional Invoice information
                            $orderPdfGeneration = $paymentHelper->addInfoToInvoice($paymentInformation, $order);
                            if ($orderPdfGeneration instanceof OrderPdfGeneration) {
                                $event->addOrderPdfGeneration($orderPdfGeneration);
                            }
                            break;
                        default:
                            //do nothing
                            break;
                    }
                } catch (\Exception $e) {
                    $logger->exception(
                        'translation.exception',
                        [
                            'error' => $e->getMessage()
                        ]
                    );

                    throw $e;
                }
            }
        );
    }

    /**
     * Return an array of events
     *
     * @return array
     */
    private function paymentMethodEvents(): array
    {
        return [
            AfterBasketItemAdd::class,
            AfterBasketCreate::class,
            FrontendLanguageChanged::class,
        ];
    }
}
