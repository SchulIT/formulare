<?php

namespace App\Security\User;

use LightSaml\Model\Protocol\Response;
use LightSaml\SpBundle\Security\User\AttributeMapperInterface;
use SchulIT\CommonBundle\Saml\ClaimTypes;
use SchulIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;
use SchulIT\CommonBundle\Security\User\AbstractUserMapper;

class AttributeMapper extends AbstractUserMapper implements AttributeMapperInterface {

    public function getAttributes(Response $response): array {
        return [
            'name_id' => $response->getFirstAssertion()->getSubject()->getNameID()->getValue(),
            'services' => $this->getServices($response)
        ];
    }

    private function getServices(Response $response): array {
        $values = $this->getValues($response, SamlClaimTypes::SERVICES);

        $services = [ ];

        foreach($values as $value) {
            $services[] = json_decode((string) $value, null, 512, JSON_THROW_ON_ERROR);
        }

        return $services;
    }

    private function getValues(Response $response, $attributeName) {
        return $response->getFirstAssertion()->getFirstAttributeStatement()
            ->getFirstAttributeByName($attributeName)->getAllAttributeValues();
    }
}