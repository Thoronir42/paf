Feature: Quote submissions can be closed

  Background:
    Given Setting "commission.quotes.enable" is "0"
    And I am a new visitor

  Scenario:
    Then I should not see "Create quote" within "nav.navbar" element
    When I open URL "create-quote"
    Then I should see "Quotes are currently closed"
