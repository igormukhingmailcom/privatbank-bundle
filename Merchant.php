<?php

namespace Mukhin\PrivatbankBundle;

use Mukhin\PrivatbankBundle\Exception\PrivatbankException;
use Mukhin\PrivatbankBundle\Model\Balance;
use Mukhin\PrivatbankBundle\Model\Statements;

class Merchant
{
    const API_URL = 'https://api.privatbank.ua/p24api';

    /** @var resource */
    protected $curl;

    /** @var string */
    protected $merchantId;

    /** @var string */
    protected $merchantSecret;

    /** @var string */
    protected $cardNumber;

    /**
     * @param string $merchantId
     * @param string $merchantSecret
     */
    public function __construct($merchantId, $merchantSecret, $cardNumber)
    {
        $this->curl = curl_init();
        $this->merchantId = $merchantId;
        $this->merchantSecret = $merchantSecret;
        $this->cardNumber = $cardNumber;
    }

    public function getCardNumber()
    {
        return $this->cardNumber;
    }

    /**
     * Close curl
     */
    public function __destruct()
    {
        $this->curl && curl_close($this->curl);
    }

    /**
     * @see https://api.privatbank.ua/#p24/balance
     *
     * @return Balance
     */
    public function getBalance()
    {
        $request = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><request version=\"1.0\"></request>");

        # Data
        $data = $request->addChild('data');
        $data->addChild('oper', 'cmt');
        $data->addChild('wait', '0');
        $data->addChild('test', '0');

        # Data > Payment
        $payment = $data->addChild('payment');
        $payment->addAttribute('id', '');

        # Data > Payment > Cardnum
        $cardnum = $payment->addChild('prop');
        $cardnum->addAttribute('name', 'cardnum');
        $cardnum->addAttribute('value', $this->cleanupCardNumber($this->cardNumber));

        # Data > Payment > Country
        $country = $payment->addChild('prop');
        $country->addAttribute('name', 'country');
        $country->addAttribute('value', 'UA');

        # Merchant
        $merchant = $request->addChild('merchant');
        $merchant->addChild('id', $this->merchantId);
        $merchant->addChild('signature', $this->buildSignature($this->innerXML($data)));

        $response = $this->call('balance', $request->asXML());
        return Balance::fromResponse(
            $response->data->info->cardbalance
        );
    }

    /**
     * @see https://api.privatbank.ua/#p24/orders
     *
     * @return Statements
     */
    public function getHistory(\DateTime $sinceDate, \DateTime $toDate = null)
    {
        if (null === $toDate) {
            $toDate = new \DateTime();
        }

        $request = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><request version=\"1.0\"></request>");

        # Data
        $data = $request->addChild('data');
        $data->addChild('oper', 'cmt');
        $data->addChild('wait', '0');
        $data->addChild('test', '0');

        # Data > Payment
        $payment = $data->addChild('payment');
        $payment->addAttribute('id', '');

        # Data > Payment > Cardnum
        $cardnum = $payment->addChild('prop');
        $cardnum->addAttribute('name', 'cardnum');
        $cardnum->addAttribute('value', $this->cleanupCardNumber($this->cardNumber));

        # Data > Payment > Start Date
        $country = $payment->addChild('prop');
        $country->addAttribute('name', 'sd');
        $country->addAttribute('value', $sinceDate->format('d.m.Y'));

        # Data > Payment > End Date
        $country = $payment->addChild('prop');
        $country->addAttribute('name', 'ed');
        $country->addAttribute('value', $toDate->format('d.m.Y'));

        # Merchant
        $merchant = $request->addChild('merchant');
        $merchant->addChild('id', $this->merchantId);
        $merchant->addChild('signature', $this->buildSignature($this->innerXML($data)));

        $response = $this->call('rest_fiz', $request->asXML());

        return Statements::fromResponse(
            $response->data->info->statements
        );
    }

    /**
     * @param string $cardNumber
     * @return string
     */
    protected function cleanupCardNumber($cardNumber)
    {
        return preg_replace('/[^0-9]/', '', $cardNumber);
    }

    /**
     * @param string $data
     * @return string
     */
    protected function buildSignature($data)
    {
        return sha1(md5(sprintf(
            '%s%s',
            $data,
                $this->merchantSecret
        )));
    }

    /**
     * @param \SimpleXMLElement $node
     * @return string
     */
    protected function innerXML(\SimpleXMLElement $node)
    {
        $content = "";
        foreach($node->children() as $child) {
            $content .= $child->asXML();
        }
        return $content;
    }

    /**
     * Call method
     *
     * @param string $method
     * @param string|null $data
     * @return \SimpleXMLElement
     */
    protected function call($method = '', $data = null)
    {
        $options = [
            CURLOPT_URL => sprintf('%s/%s', self::API_URL, $method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => null,
            CURLOPT_POSTFIELDS => null,
        ];

        if ($data) {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = $data;
        }

        $response = new \SimpleXMLElement($this->executeCurl($options));

        if (isset($response->data->error)) {
            throw new PrivatbankException((string)$response->data->error['message']);
        }

        // Fuck, Privatbank, you kidding me?
        if (isset($response->data->info->error)) {
            throw new PrivatbankException((string)$response->data->info->error);
        }

        return $response;
    }

    /**
     * @param array $options
     * @return string
     */
    protected function executeCurl(array $options)
    {
        curl_setopt_array($this->curl, $options);

        $result = curl_exec($this->curl);
        if ($result === false) {
            throw new PrivatbankException(curl_error($this->curl), curl_errno($this->curl));
        }

        $httpCode = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
        if (!in_array($httpCode, array(200))) {
            throw new PrivatbankException(sprintf('Server returned HTTP code %s', $httpCode), $httpCode);
        }
        return $result;
    }
}
