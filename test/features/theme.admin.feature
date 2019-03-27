# time:0m26s
@api @javascript @disablecaptcha
Feature: Theme

  Background:
    Given pages:
      | name          | url                                     |
      | Appearance    | /admin/appearance                       |
      | Settings      | /admin/appearance/settings/nuboot_radix |
    Given users:
      | name    | mail                | roles                |
      | John    | john@example.com    | administrator         |
      | Site Manager | sitemanager@example.com | site manager |

  @noworkflow
    Scenario: Dummy scenario because behat errors if there is no scenario in a feature file
    Given I am logged in as "John"
    And I am on "Settings" page
    Then I should see "Logo image settings"
    
  @noworkflow @fixme
  Scenario: Add custom logo
    Given I am logged in as "John"
    And I am on "Settings" page
    Then I should see "Logo image settings"
    And I hide the admin menu
    And I uncheck "Use the default logo"
    And I attach the drupal file "dkan/dkan_logo.png" to "files[logo_upload]"
    And I wait for the file upload to finish
    When I press "Save configuration"
    Then I wait for "3" seconds
    Then I should see "The configuration options have been saved"

  @noworkflow @customizable @fixme
  Scenario: Add custom hero image
    Given I am logged in as "John"
    And I am on "Settings" page
    Then I should see "Hero Unit"
    And I attach the drupal file "dkan/dkan_hero.jpg" to "files[hero_file]"
    And I wait for the file upload to finish
    When I press "Save configuration"
    Then I wait for "3" seconds
    Then I should see "The configuration options have been saved"

  @noworkflow @fixme
  Scenario: Add custom site information
    Given I am logged in as "Site Manager"
    And I hide the admin menu
    Then I am on "Settings" page
    And I should see "E-mail address"
    And I fill in "Site name" with "sitename test"
    And I fill in "Slogan" with "slogan test"
    And I fill in "E-mail address" with "test@example.com"
    When I press "Save configuration"
    Then I wait for "3" seconds
    Then I should see "The configuration options have been saved"
