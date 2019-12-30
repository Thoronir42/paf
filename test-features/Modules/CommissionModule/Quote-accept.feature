Feature: Create and accept quote

  Scenario: Guest creates an quote
    Given Setting "commission.quotes.enable" is "1"
    And I am on "/create-quote"
    Then I should see "Contact information"
    When I fill in the following:
    | contact[email] | john@maden.com |
    | fursuit[name] | Hoba |
    | fursuit[characterDescription] | Black and white mofo. Doesn't give a shit. OwO |
    | fursuit[type] | fullSuit |
    And I attach the file "test-features/files/ref_01.jpg" to "reference[]"
    And I press "Submit"
    Then I should see "Your quote has been received"

    Scenario: Maker accepts a quote
      Given I sign in as "the-y:test"
      And I am on "/quotes/list"
      When I accept quote "Hoba"
      Then I should not see "Hoba" in the ".quote-overview" element
      When I am on "/commissions"
      Then I should see "Hoba"
