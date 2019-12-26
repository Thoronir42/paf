Feature: Quotes can be rejected

  Scenario: Guest creates an quote
    Given Setting "commission.quotes.enable" is "1"
    And I am on "/create-quote"
    Then I should see "Contact information"
    When I fill in the following:
    | contact[email] | john@maden.com |
    | fursuit[name] | Hedge |
    | fursuit[characterDescription] | Runs. Also is a ring master |
    | fursuit[type] | partial |
    And I attach the file "test-features/files/ref_01.jpg" to "reference[]"
    And I press "Submit"
    Then I should see "Your quote has been received"

    Scenario: Maker rejects a quote
      Given I sign in as "the-y:test"
      And I am on "/quotes/list"
      When I reject quote "Hedge"
      Then I should not see "Hedge" in the ".quote-overview" element
      When I am on "/cases/list"
      Then I should not see "Hedge"
