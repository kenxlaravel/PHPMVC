<?php

class ProductStateParameter {

    /**
     * Property codes have the following restrictions:
     *     - Do not use the letter 'z'.
     *     - Do not re-use the same code more than once.
     *     - The code must be exactly two characters.
     */
    private static $propertyGroups = array (
        array (
            'stockTool',
            'easyDesignTool',
            'advancedDesignTool',
            'streetSignDesignTool'
        ),
        array (
            'translationFamily'
        ),
        array (
            'preconfiguredSku'
        ),
        array (
            'sourceProduct',
            'sourceProductRecommendation',
            'sourceAccessoryFamilyProduct',
            'sourceInstallationAccessory',
            'sourceLandingProduct',
            'sourceSubcategoryProduct'
        ),
        array (
            'breadcrumbSubcategory',
            'breadcrumbLanding'
        )
    );

    /**
     * Property codes have the following restrictions:
     *     - Do not use the letter 'z'.
     *     - Do not re-use the same code more than once.
     *     - The code must be exactly two characters.
     */
    private static $propertyCodes = array (
        'sourceProduct'                => 'pr',
        'sourceProductRecommendation'  => 'rp',
        'sourceAccessoryFamilyProduct' => 'ap',
        'sourceInstallationAccessory'  => 'ia',
        'sourceLandingProduct'         => 'lp',
        'sourceSubcategoryProduct'     => 'sp',
        'preconfiguredSku'             => 'sk',
        'stockTool'                    => 'st',
        'easyDesignTool'               => 'ed',
        'advancedDesignTool'           => 'ad',
        'streetSignDesignTool'         => 'sd',
        'translationFamily'            => 'tf',
        'breadcrumbSubcategory'        => 'bs',
        'breadcrumbLanding'            => 'bl'
    );

    /**
     * Supported types:
     *     - numeric
     *     - boolean
     */
    private static $propertyTypes = array (
        'sourceProduct'                => 'numeric',
        'sourceProductRecommendation'  => 'numeric',
        'sourceAccessoryFamilyProduct' => 'numeric',
        'sourceInstallationAccessory'  => 'numeric',
        'sourceLandingProduct'         => 'numeric',
        'sourceSubcategoryProduct'     => 'numeric',
        'preconfiguredSku'             => 'numeric',
        'stockTool'                    => 'boolean',
        'easyDesignTool'               => 'numeric',
        'advancedDesignTool'           => 'numeric',
        'streetSignDesignTool'         => 'numeric',
        'translationFamily'            => 'numeric',
        'breadcrumbSubcategory'        => 'numeric',
        'breadcrumbLanding'            => 'numeric'
    );

    /**
     * A dependent property is one that will be ignored unless its parent is present.
     * These are defined in the form of `'dependentProperty' => 'parentProperty'`.
     */
    private static $dependentProperties = array (
        'translationFamily' => 'stockTool'
    );

    private static $separator           = 'z';

    private static $decodedNumericBase  = 10;

    private static $encodedNumericBase  = 35;

    private static function encodePropertyName ($propertyName) {

        return isset(self::$propertyCodes[$propertyName]) ? self::$propertyCodes[$propertyName] : NULL;
    }

    private static function decodePropertyName ($encodedPropertyCodes) {

        $reversePropertyCodes = array_flip (self::$propertyCodes);

        return isset($reversePropertyCodes[$encodedPropertyCodes]) ? $reversePropertyCodes[$encodedPropertyCodes] : NULL;
    }

    private static function encodeValue ($value, $type = 'numeric') {

        return $type === 'numeric' ? self::encodeNumericValue ($value) : self::encodeBooleanValue ($value);
    }

    private static function encodeNumericValue ($value, $type = 'numeric') {

        return base_convert ($value, self::$decodedNumericBase, self::$encodedNumericBase);
    }

    private static function encodeBooleanValue ($value) {

        return isset($value) ? 1 : NULL;
    }

    private static function decodeValue ($value, $type = 'numeric') {

        return $type === 'numeric' ? self::decodeNumericValue ($value) : self::decodeBooleanValue ($value);
    }

    private static function decodeNumericValue ($value, $type = 'numeric') {

        return base_convert ($value, self::$encodedNumericBase, self::$decodedNumericBase);
    }

    private static function decodeBooleanValue ($value) {

        return isset($value) ? 1 : NULL;
    }

    public static function encode ($state) {

        $properties = array ();

        // Encode the properties.
        foreach (self::$propertyGroups as $propertyGroup) {
            foreach ($propertyGroup as $property) {
                if( isset($state[$property]) ) {
                    $properties[self::encodePropertyName ($property)] = self::encodeValue (
                        $state[$property], self::$propertyTypes[$property]
                    );
                }
            }
        }

        // Remove unqualified dependent properties.
        foreach ($properties as $propertyCode => $value) {
            $propertyName = self::decodePropertyName ($propertyCode);
            if( array_key_exists ($propertyName, self::$dependentProperties
                ) && !isset($properties[self::encodePropertyName (self::$dependentProperties[$propertyName])])
            ) {

                unset($properties[$propertyCode]);
            }
        }

        // Combine the properties and values into strings.
        $combinedKeyValueArray = array ();
        foreach ($properties as $propertyCode => $value) {
            $combinedKeyValueArray[] = $propertyCode.$value;
        }

        // Implode and return.
        return implode (self::$separator, $combinedKeyValueArray);
    }

    public static function decode ($parameter) {

        $state = array ();

        // Parse the parameter.
        foreach (explode (self::$separator, $parameter) as $keyValueString) {
            $property = self::decodePropertyName (mb_substr ($keyValueString, 0, 2));
            $state[$property] = self::decodeValue (mb_substr ($keyValueString, 2), self::$propertyTypes[$property]);
        }

        // Remove unqualified dependent properties.
        foreach ($state as $property => $value) {
            if( array_key_exists (
                    $property, self::$dependentProperties
                ) && !isset($state[self::$dependentProperties[$property]])
            ) {
                unset($state[$property]);
            }
        }

        return $state;
    }
}
