Feature: Sign

  @SmokeTest
  Scenario: Sign in and reset
    When I sign in as "T-boi:test"
    Then I should see "Sign out"
    When I am new visitor
    Then I should see "Sign in"

  Scenario: Sign in and out
    When I sign in as "T-boi:test"
    Then I should see "Sign out"
    When I sign out
    Then I should see "Sign in"


