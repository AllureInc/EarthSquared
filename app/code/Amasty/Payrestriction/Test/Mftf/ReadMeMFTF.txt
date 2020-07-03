ReadeMeMFTF (recommendations for running tests related to Payment Restriction extension).

    29 Payment Restrictions specific tests.

        Tests group: Payrestriction
        Runs all tests except PayrestrictionCheckExternalPayments (online payment methods) group.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group Payrestriction -r

        -----

        Here and below:
        to run 7 tests related to payment methods, it is necessary to add test credentials for needed methods at (for Composer based installs)
        /magento/vendor/amasty/module-common-tests/Test/Mftf/Data/PaymentMethodsCredentialsData
        or (for install-by-upload)
        /magento/app/code/Amasty/CommonTests/Test/Mftf/Data/PaymentMethodsCredentialsData

        (!) Please note that due to test framework limitations it is currently impossible to revert payment methods configuration to pre-test-run condition.
        To avoid cases where live Magento instances may end up with test/sandbox payment methods mode after running tests, online payment method configuration parts (such as API Username/Password, Merchant ID, etc) will be emptied after its tests run.
        Please make sure that all relevant details are at hand prior to running this group of tests (and/or particular tests from it) at live instances.

        Tests group: PayrestrictionCheckExternalPayments
        Runs tests related to external payment methods' work with Payment Restriction.
            SSH command to run this group of tests:
            vendor/bin/mftf run:group PayrestrictionCheckExternalPayments -r
        Included payment method tests:
        Amazon, Authorise and EWay, Braintree, Klarna, PayPal, PayflowPro and PayPalPro, Stripe
            SSH command to run tests for particular payment method:
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsAmazon -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsAuthorizeAndEWay -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsBraintree -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsKlarna -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsPayPal -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsPayPalPayflowAndPro -r
            vendor/bin/mftf run:group PayrestrictionCheckExternalPaymentsStripe -r