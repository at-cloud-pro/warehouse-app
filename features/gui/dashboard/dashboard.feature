Feature:
    As a user
    I can see the dashboard

    Scenario: Any user can access the dashboard
        When I go to "/"
        Then I expect response status code to be 200
