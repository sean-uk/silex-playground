Feature: Stack pop
  In order to retrieve JSON records added earlier
  As an API user
  I need to be able to pop previously added objects from the stack

  Rules:
  - There are no parameters.
  - Items are removed stack style: last in first out
  - The return value will be a JSON object with the same data as pushed earlier.
  - 'null' will be returned if the stack is empty

  Scenario: Popping an empty stack
    Given the stack is empty
    When I pop the stack
    Then I should get a null response

  Scenario: Popping a stack with one item in it
    Given the stack is empty
    And this has been pushed onto the stack: '{"value":"somethingorother"}'
    When I pop the stack
    Then the response JSON should be: '{"value":"somethingorother"}'

  Scenario: Popping a stack with multiple items on it
    Given the stack is empty
    And this has been pushed onto the stack: '{"id":"ABC"}'
    And this has been pushed onto the stack: '{"str":"1234", "array": [1,2,3,4], "obj":{"k":"v"}}'
    When I pop the stack
    Then the response JSON should be: '{"str":"1234", "array": [1,2,3,4], "obj":{"k":"v"}}'

  Scenario: Popping a stack repeatedly
    Given the stack is empty
    And this has been pushed onto the stack: '{}'
    And this has been pushed onto the stack: '{"hello":"world"}'
    When I pop the stack
    And I pop the stack
    Then the response JSON should be: '{}'