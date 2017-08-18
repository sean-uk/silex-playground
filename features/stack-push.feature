Feature: Stack push
  In order to record structured data
  As an API user
  I need to be able to post arbitrary JSON objects

  Rules:
    The request body must be a single JSON object
    The response will be a JSON object containing a 'success' bool and a 'error_message' string

  Scenario: Posting an empty JSON object
    Given I post an empty JSON object
    Then the response success should be true
    And the response error message should be empty

  Scenario: Posting a JSON object with some stuff in it
    Given I post the JSON object
      """
      {"id":1, "value":"something"}
      """
    Then the response success should be true
    And the response error message should be empty