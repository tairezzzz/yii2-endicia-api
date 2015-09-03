<?php

namespace tairezzzz\endicia;

/**
 * This is just an example.
 */

class EndiciaAPI extends \yii\base\Component
{
    /**
     * Client Object from Guzzle
     * @var [type]
     */
    private $client;
    /**
     * Requester ID from Endicia
     * @var [type]
     */
    private $RequesterID;
    /**
     * Account ID from Endicia
     * @var [type]
     */
    private $AccountID;
    /**
     * Passphrase from Endicia
     * @var string
     */
    private $PassPhrase;
    /**
     * Unique Transaction ID
     * @var string
     */
    public $PartnerTransactionID;

    /**
     * Constructor
     * @param string $RequesterID
     * @param string $AccountID
     * @param string $PassPhrase
     */
    function __construct($RequesterID, $AccountID, $PassPhrase)
    {
        $this->client = new \GuzzleHttp\Client(['headers' => ['Content-Type' => 'application/x-www-form-urlencoded']]);
        $this->RequesterID = $RequesterID;
        $this->AccountID = $AccountID;
        $this->PassPhrase = $PassPhrase;
    }

    /**
     * Sends A requst for shipping label and info to endicia
     * @param  array $data Array of data to be formated as XML see Endicia documentation
     * @return array The Response data from Endicia as an array , endica actually returns XML
     */
    public function request_shipping_label($data)
    {
        // Note you may want to associate this in some other way, like with a database ID.
        $this->PartnerTransactionID = substr(uniqid(rand(), true), 0, 10);
        $xml = '<LabelRequest ImageFormat="GIF" Test="YES">
					<RequesterID>' . $this->RequesterID . '</RequesterID>
					<AccountID>' . $this->AccountID . '</AccountID>
					<PassPhrase>' . $this->PassPhrase . '</PassPhrase>
					<PartnerTransactionID>' . $this->PartnerTransactionID . '</PartnerTransactionID>';
        if (!empty($data)) {
            foreach ($data as $node_key => $node_value) {
                $xml .= '<' . $node_key . '>' . $node_value . '</' . $node_key . '>';
            }
        }

        $xml .= '<ResponseOptions PostagePrice="TRUE"/>
				</LabelRequest>';
        $data = ['labelRequestXML' => $xml];
        $response = $this->client->get('https://labelserver.endicia.com/LabelService/EwsLabelService.asmx/GetPostageLabelXML', ['query' => $data]);
        return $response->xml();
    }

}
