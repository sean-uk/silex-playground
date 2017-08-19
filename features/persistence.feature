Feature: Stack persistence
  In order to store data indefinitely
  As an API user
  I need the state of the stack to persist across requests

  Rules:
  - The stack state is global and the same for everyone (not session based etc)

  Scenario: Popping from a stack pushed to under a different session
    Given the stack is empty
    When I post the JSON object '{"hello": "world"}'
    And I start a new session
    And I pop the stack
    And the response JSON should be: '{"hello": "world"}'