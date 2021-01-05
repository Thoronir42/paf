Feature: Quotes can be rejected

  Background:
    Given Setting "commission.quotes.enable" is "1"

  Scenario: Guest creates an quote
    And I navigate to "Create quote"
    Then I should see "Contact information"
    When I fill in a form with:
    | contact[email]                | john@maden.com              |
    | fursuit[name]                 | Hedge                       |
    | fursuit[characterDescription] | Runs. Also is a ring master |
    | fursuit[type]                 | Partial suit                |
    And I select file "ref_01.jpg" to field "reference[]"
    And I click on "Submit"
    Then I should see "Your quote has been received"

    Scenario: Maker rejects a quote
      Given I sign in as "the-y:test"
      And I navigate to "Dashboard > Quotes"
      When I reject quote "Hedge"
      Then I should not see "Hedge" within ".quote-list" element
      When I navigate to "Dashboard > Commissions"
      Then I should not see "Hedge" within ".datagrid-commissions" element
