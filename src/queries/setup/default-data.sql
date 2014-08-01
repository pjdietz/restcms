-- Set session variables based on global constants.

SET @groupAdmin = {GROUP_ADMIN};
SET @groupContributor = {GROUP_CONTRIBUTOR};
SET @groupConsumer = {GROUP_CONSUMER};

SET @privReadArticle = {PRIV_READ_ARTICLE};
SET @privCreateArticle = {PRIV_CREATE_ARTICLE};
SET @privModifyArticle = {PRIV_MODIFY_ARTICLE};
SET @privModifyAnyArticle = {PRIV_MODIFY_ANY_ARTICLE};

SET @statusDraft = {STATUS_DRAFT};
SET @statusPublished = {STATUS_PUBLISHED};
SET @statusPending = {STATUS_PENDING};
SET @statusRemoved = {STATUS_REMOVED};

-- Create default statuses.
INSERT INTO status (statusId, statusSlug, statusName) VALUES
(@statusDraft, 'draft', 'Draft'),
(@statusPublished, 'published', 'Published'),
(@statusPending, 'pending', 'Pending Review'),
(@statusRemoved, 'removed', 'Removed');

-- Create default user privileges
INSERT INTO userPrivilege (
    userPrivilegeId,
    dateCreated,
    dateModified,
    privilegeName
) VALUES
(@privReadArticle, NOW(), NOW(), 'Read Article'),
(@privCreateArticle, NOW(), NOW(), 'Create Article'),
(@privModifyArticle, NOW(), NOW(), 'Modify Article'),
(@privModifyAnyArticle, NOW(), NOW(), 'Modify Any Article')
;

-- Create default user groups
INSERT INTO userGroup (
    userGroupId,
    dateCreated,
    dateModified,
    groupName
) VALUES
(@groupAdmin, NOW(), NOW(), 'Admin'),
(@groupContributor, NOW(), NOW(), 'Contributor'),
(@groupConsumer, NOW(), NOW(), 'Consumer');

-- Assign privileges to groups
INSERT INTO userGroupPrivilege (
    dateCreated,
    dateModified,
    userGroupId,
    userPrivilegeId
) VALUES
-- Admin
(NOW(), NOW(), @groupAdmin, @privReadArticle),
(NOW(), NOW(), @groupAdmin, @privCreateArticle),
(NOW(), NOW(), @groupAdmin, @privModifyAnyArticle),
-- Contributor
(NOW(), NOW(), @groupContributor, @privReadArticle),
(NOW(), NOW(), @groupContributor, @privCreateArticle),
(NOW(), NOW(), @groupContributor, @privModifyArticle),
-- Consumer
(NOW(), NOW(), @groupConsumer, @privReadArticle);

-- Create default users and groups.
INSERT INTO user (
    dateCreated,
    dateModified,
    username,
    passwordHash,
    displayName,
    userGroupId
) VALUES
(NOW(), NOW(), 'admin', 'admin', 'Administrator', @groupAdmin),
(NOW(), NOW(), 'contributor', 'contributor', 'Contributor', @groupContributor),
(NOW(), NOW(), 'consumer', 'consumer', 'Consumer', @groupConsumer);
