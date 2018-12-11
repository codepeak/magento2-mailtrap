<?php

namespace Codepeak\Mailtrap\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\MessageInterface;

/**
 * Class Transport
 *
 * @package  Codepeak\Mailtrap\Model
 * @license  MIT License https://opensource.org/licenses/MIT
 * @author   Robert Lord, Codepeak AB <robert@codepeak.se>
 * @link     https://codepeak.se
 */
class Transport extends \Zend_Mail_Transport_Smtp implements \Magento\Framework\Mail\TransportInterface
{
    /**
     * @var \Magento\Framework\Mail\MessageInterface
     */
    protected $_message;

    /**
     * Transport constructor.
     *
     * @param MessageInterface     $message
     * @param ScopeConfigInterface $scopeConfig
     *
     * @throws \Exception
     */
    public function __construct(MessageInterface $message, ScopeConfigInterface $scopeConfig)
    {
        // Make sure we're enabled
        if ($scopeConfig->getValue('mailtrap/general/enabled') == '1') {
            // Assure message is an instance of Zend_Mail
            if (!$message instanceof \Zend_Mail) {
                throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
            }

            // Setup SMTP details
            $smtpHost = 'smtp.mailtrap.io';
            $smtpConf = [
                'auth'     => 'login',
                'tsl'      => 'tsl',
                'port'     => '2525',
                'username' => $scopeConfig->getValue('mailtrap/general/username'),
                'password' => $scopeConfig->getValue('mailtrap/general/password')
            ];

            parent::__construct($smtpHost, $smtpConf);
        }
        $this->_message = $message;
    }

    /**
     * Send a mail using this transport
     * @return void
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMessage()
    {
        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\MailException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_message;
    }
}