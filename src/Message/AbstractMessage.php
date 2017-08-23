<?php

namespace NordeaPayment\Message;

/**
 * AbstractMessages eases message creation using DOM
 */
abstract class AbstractMessage implements MessageInterface
{
    const SCHEMA_LOCATION = '%s file://Client/H$/SchemaXML/pain.001.001.03.xsd';

    /**
     * Builds the DOM of the actual message
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    abstract protected function buildDom(\DOMDocument $doc);

    /**
     * Gets the name of the schema
     *
     * @return string
     */
    abstract protected function getSchemaName();

    /**
     * Builds a DOM document of the message
     *
     * @return \DOMDocument
     */
    public function asDom()
    {
        $schema = $this->getSchemaName();
        $ns = 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03';
        $schemaLocation = $ns . ' file://Client/H$/SchemaXML/pain.001.001.03.xsd';

        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $root = $doc->createElement('Document');
        $root->setAttribute('xmlns', $ns);
        $root->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $root->setAttribute('xsi:schemaLocation', $schemaLocation);
        $root->appendChild($this->buildDom($doc));
        $doc->appendChild($root);

        return $doc;
    }

    /**
     * {@inheritdoc}
     */
    public function asXml()
    {
        return $this->asDom()->saveXML();
    }

    /**
     * Returns the name of the software used to create the message
     *
     * @return string
     */
    public function getSoftwareName()
    {
        return 'NordeaPayment';
    }

    /**
     * Returns the version of the software used to create the message
     *
     * @return string
     */
    public function getSoftwareVersion()
    {
        return '0.0.1';
    }

    /**
     * Creates a DOM element which contains details about the software used to create the message
     *
     * @param \DOMDocument $doc
     *
     * @return \DOMElement
     */
    protected function buildContactDetails(\DOMDocument $doc)
    {
        $root = $doc->createElement('CtctDtls');

        $root->appendChild($doc->createElement('Nm', $this->getSoftwareName()));
        $root->appendChild($doc->createElement('Othr', $this->getSoftwareVersion()));

        return $root;
    }
    protected function buildInitiatingParty(\DOMDocument $doc, $name, $id)
    {
        $code = $doc->createElement('Cd', 'CUST');

        $schemeName = $doc->createElement('SchmeNm');
        $schemeName->appendChild($code);

        $identification2 = $doc->createElement('Id', $id);


        $other = $doc->createElement('Othr');
        $other->appendChild($identification2);
        $other->appendChild($schemeName);

        $organisationIdentification = $doc->createElement('OrgId');
        $organisationIdentification->appendChild($other);

        $identification = $doc->createElement('Id');
        $identification->appendChild($organisationIdentification);

        $name = $doc->createElement('Nm', $name);

        $initiatingParty = $doc->createElement('InitgPty');
        $initiatingParty->appendChild($name);
        $initiatingParty->appendChild($identification);

        return $initiatingParty;
    }
}
