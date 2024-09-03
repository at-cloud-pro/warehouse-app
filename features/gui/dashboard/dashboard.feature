Feature:
    As a user
    I can see the dashboard

    Scenario: It receives a response from Symfony's kernel
        When a demo scenario sends a request to "/"
        Then the response should be received
