Feature: Sign

  @SmokeTest
  Scenario: Sign in and reset
    Given I am a new visitor
    Then I should see "Sign in"
    When I sign in as "T-boi:test"
    Then I should see "Sign out"
    When I am a new visitor
    Then I should see "Sign in"

  Scenario: Sign in and out
    Given I am a new visitor
    When I sign in as "T-boi:test"
    Then I should see "Sign out"
    When I sign out
    Then I should see "Sign in"


