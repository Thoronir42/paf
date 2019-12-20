Feature: Quotes

  Scenario: Guest creates an quote
    Given I am new visitor
    And I am on homepage
    And Setting "commission.quotes.enable" is "1"
    And I am on "/quotes"
    Then I should see "Contact information"
    When I fill in the following:
    | contact[email] | john@maden.com |
    | fursuit[name] | Hoba |
    | fursuit[characterDescription] | Black and white mofo. Doesn't give a shit. OwO |
    | fursuit[type] | fullsuit |
    And I attach the file "test-features/files/ref_01.jpg" to "reference[]"
    And I press "Submit"
    Then I should see "Your quote has been received"

