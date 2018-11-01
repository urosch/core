@webUI @insulated @disablePreviews
Feature: Locks
  As a user
  I would like to be able to delete locks of files and folders
  So that I can access files with locks that have not been cleared

  Background:
    #do not set email, see bugs in https://github.com/owncloud/core/pull/32250#issuecomment-434615887
    Given these users have been created:
      |username      |
      |brand-new-user|
    And user "brand-new-user" has logged in using the webUI

  Scenario: setting a lock shows the lock symbols at the correct files/folders
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
      | lockscope | shared |
    And the user "brand-new-user" has locked the file "data.zip" setting following properties
      | lockscope | exclusive |
    When the user browses to the files page
    Then the folder "simple-folder" should be marked as locked on the webUI
    And the folder "simple-folder" should be marked as locked by user "brand-new-user" in the locks tab of the details panel on the webUI
    But the folder "simple-empty-folder" should not be marked as locked on the webUI
    And the file "data.zip" should be marked as locked on the webUI
    And the file "data.zip" should be marked fas locked by user "brand-new-user" in the locks tab of the details panel on the webUI
    But the file "data.tar.gz" should not be marked as locked on the webUI

  Scenario: lock set on a shared file shows the lock information for all involved users
    Given these users have been created:
      |username  |
      |sharer    |
      |receiver  |
      |receiver2 |
    And group "receiver-group" has been created
    And user "receiver2" has been added to group "receiver-group"
    And user "sharer" has shared file "data.zip" with user "receiver"
    And user "sharer" has shared file "data.tar.gz" with group "receiver-group"
    And user "receiver" has shared file "data (2).zip" with user "brand-new-user"
    And the user "sharer" has locked the file "data.zip" setting following properties
      | lockscope | shared |
    And the user "receiver" has locked the file "data (2).zip" setting following properties
      | lockscope | shared |
    And the user "brand-new-user" has locked the file "data (2).zip" setting following properties
      | lockscope | shared |
    And the user "receiver2" has locked the file "data.tar (2).gz" setting following properties
      | lockscope | shared |
    When the user browses to the files page
    Then the file "data (2).zip" should be marked as locked on the webUI
    And the file "data (2).zip" should be marked as locked by user "sharer" in the locks tab of the details panel on the webUI
    And the file "data (2).zip" should be marked as locked by user "receiver" in the locks tab of the details panel on the webUI
    And the file "data (2).zip" should be marked as locked by user "brand-new-user" in the locks tab of the details panel on the webUI
    But the file "data.zip" should not be marked as locked on the webUI
    When the user re-logs in as "sharer" using the webUI
    Then the file "data.zip" should be marked as locked on the webUI
    And the file "data.zip" should be marked as locked by user "sharer" in the locks tab of the details panel on the webUI
    And the file "data.zip" should be marked as locked by user "receiver" in the locks tab of the details panel on the webUI
    And the file "data.zip" should be marked as locked by user "brand-new-user" in the locks tab of the details panel on the webUI
    And the file "data.tar.gz" should be marked as locked on the webUI
    And the file "data.tar.gz" should be marked as locked by user "receiver2" in the locks tab of the details panel on the webUI
    When the user re-logs in as "receiver2" using the webUI
    Then the file "data.tar (2).gz" should be marked as locked on the webUI
    And the file "data.tar (2).gz" should be marked as locked by user "receiver2" in the locks tab of the details panel on the webUI

  Scenario: setting a lock on a folder shows the symbols at the sub-elements
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | shared |
    When the user opens the folder "simple-folder" using the webUI
    Then the folder "simple-empty-folder" should be marked as locked on the webUI
    And the folder "simple-empty-folder" should be marked as locked by user "brand-new-user" in the locks tab of the details panel on the webUI
    And the file "data.zip" should be marked as locked on the webUI
    And the file "data.zip" should be marked as locked by user "brand-new-user" in the locks tab of the details panel on the webUI

  Scenario: setting a depth:0 lock on a folder does not shows the symbols at the sub-elements
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | depth | 0 |
    When the user browses to the files page
    Then the folder "simple-folder" should be marked as locked on the webUI
    When the user opens the folder "simple-folder" using the webUI
    Then the folder "simple-empty-folder" should not be marked as locked on the webUI
    And the file "data.zip" should not be marked as locked on the webUI

  Scenario: unlocking by webDAV deletes the lock symbols at the correct files/folders
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | shared |
    When the user "brand-new-user" unlocks the last created lock of the folder "simple-folder" using the WebDAV API
    And the user browses to the files page
    Then the folder "simple-folder" should not be marked as locked on the webUI

  Scenario Outline: deleting the only remaining lock of a file/folder
    Given the user "brand-new-user" has locked the file "lorem.txt" setting following properties
     | lockscope | <lockscope> |
    And the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | <lockscope> |
    And the user has browsed to the files page
    When the user unlocks the lock no 1 of the file "lorem.txt" on the webUI
    And the user unlocks the lock no 1 of the folder "simple-folder" on the webUI
    Then the file "lorem.txt" should not be marked as locked on the webUI
    And the folder "simple-folder" should not be marked as locked on the webUI
    And 0 locks should be reported for the file "lorem.txt" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the folder "simple-folder" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the file "simple-folder/lorem.txt" of user "brand-new-user" by the WebDAV API
    Examples:
      | lockscope |
      | exclusive |
      | shared    |

  Scenario Outline: deleting the only remaining lock of a file/folder and reloading the page
    Given the user "brand-new-user" has locked the file "lorem.txt" setting following properties
     | lockscope | exclusive |
    And the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | exclusive |
    And the user has browsed to the files page
    When the user unlocks the lock no 1 of the file "lorem.txt" on the webUI
    And the user unlocks the lock no 1 of the folder "simple-folder" on the webUI
    And the user reloads the current page of the webUI
    Then the file "lorem.txt" should not be marked as locked on the webUI
    And the folder "simple-folder" should not be marked as locked on the webUI
    And 0 locks should be reported for the file "lorem.txt" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the folder "simple-folder" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the file "simple-folder/lorem.txt" of user "brand-new-user" by the WebDAV API
    Examples:
      | lockscope |
      | exclusive |
      | shared    |

  Scenario Outline: deleting the only remaining lock of a folder by deleting it from a file in the folder
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | <lockscope> |
    And the user has browsed to the files page
    And the user has opened the folder "simple-folder" using the webUI
    When the user unlocks the lock no 1 of the file "lorem.txt" on the webUI
    Then the file "lorem.txt" should not be marked as locked on the webUI
    And the folder "simple-empty-folder" should not be marked as locked on the webUI
    When the user browses to the files page
    Then the folder "simple-folder" should not be marked as locked on the webUI
    And 0 locks should be reported for the folder "simple-folder" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the file "simple-folder/lorem.txt" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the folder "simple-folder/simple-empty-folder" of user "brand-new-user" by the WebDAV API
    Examples:
      | lockscope |
      | exclusive |
      | shared    |

  Scenario Outline: deleting the only remaining lock of a folder by deleting it from a file in the folder and reloading the page
    Given the user "brand-new-user" has locked the folder "simple-folder" setting following properties
     | lockscope | <lockscope> |
    And the user has browsed to the files page
    And the user has opened the folder "simple-folder" using the webUI
    When the user unlocks the lock no 1 of the file "lorem.txt" on the webUI
    And the user reloads the current page of the webUI
    Then the file "lorem.txt" should not be marked as locked on the webUI
    And the folder "simple-empty-folder" should not be marked as locked on the webUI
    When the user browses to the files page
    Then the folder "simple-folder" should not be marked as locked on the webUI
    And 0 locks should be reported for the folder "simple-folder" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the file "simple-folder/lorem.txt" of user "brand-new-user" by the WebDAV API
    And 0 locks should be reported for the folder "simple-folder/simple-empty-folder" of user "brand-new-user" by the WebDAV API
    Examples:
      | lockscope |
      | exclusive |
      | shared    |

  Scenario: delete one of multiple locks
  Given these users have been created:
      |username      |
      |receiver1 |
      |receiver2 |
  And user "brand-new-user" has shared file "/lorem.txt" with user "receiver1"
  And user "brand-new-user" has shared file "/lorem.txt" with user "receiver2"
  And the user "brand-new-user" has locked the file "lorem.txt" setting following properties
     | lockscope | shared |
  And the user "receiver1" has locked the file "lorem (2).txt" setting following properties
     | lockscope | shared |
  And the user "receiver2" has locked the file "lorem (2).txt" setting following properties
     | lockscope | shared |
  And the user has browsed to the files page
  When the user unlocks the lock no 1 of the file "lorem.txt" on the webUI
  Then the file "lorem.txt" should be marked as locked on the webUI
  And the file "lorem.txt" should be marked as locked by user "receiver1" in the locks tab of the details panel on the webUI
  And the file "lorem.txt" should be marked as locked by user "receiver2" in the locks tab of the details panel on the webUI
  And 2 locks should be reported for the file "lorem.txt" of user "brand-new-user" by the WebDAV API

#  Scenario: delete a lock that was created by an other user results in an error
#  Scenario: delete the first shared lock of a file
#  Scenario: delete the second shared lock of a file
#  Scenario: delete the last shared lock of a file
#  Scenario: delete the first shared lock of a folder
#  Scenario: delete the second shared lock of a folder
#  Scenario: delete the last in shared lock of a folder
#  Scenario: delete/upload/rename/move a locked file gives a nice error message
#  Scenario: unshare locked folder/file
#  Scenario: decline/accept locked folder/file
#  Scenario: correct displayname / username is shown in the lock list
#  Scenario: new files in a locked folder get locked
