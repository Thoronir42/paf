Feature: Create and accept quote

  Background:
    Given Setting "commission.quotes.enable" is "1"

  Scenario: Guest creates an quote
    And I navigate to "Create quote"
    Then I should see "Contact information"
    When I fill in a form with:
    | contact[email]                | john@maden.com                                 |
    | fursuit[name]                 | Houba                                          |
    | fursuit[characterDescription] | Black and white mofo. Doesn't give a shit. OwO |
    | fursuit[type]                 | Partial suit                                   |
    And I select file "ref_01.jpg" to field "reference[]"
    And I click on "Submit"
    Then I should see "Your quote has been received"

    Scenario: Maker accepts a quote
      Given I sign in as "the-y:test"
      And I navigate to "Dashboard > Quotes"
      When I accept quote "Houba"
      Then I should not see "Houba" within ".quote-list" element
      When I navigate to "Dashboard > Commissions"
      Then I should see "Houba" within ".datagrid-commissions" element
