-- Create default statuses.
INSERT INTO status (statusSlug, statusName) VALUES
('draft', 'Draft'),
('published', 'Published'),
('pending', 'Pending Review');

SET @privReadContent = {PRIV_READ_ARTICLE};
SET @privCrreateContent = {PRIV_CREATE_ARTICLE};

SET @groupAdmin = 1;
SET @groupConsumer = 2;

-- Create default user privileges
INSERT INTO userPrivilege (
    userPrivilegeId,
    dateCreated,
    dateModified,
    privilegeName
) VALUES
(@privReadContent, NOW(), NOW(), 'Read Content'),
(@privCrreateContent, NOW(), NOW(), 'Create Content');

-- Create default user groups
INSERT INTO userGroup (
    userGroupId,
    dateCreated,
    dateModified,
    groupName
) VALUES
(@groupConsumer, NOW(), NOW(), 'Consumer'),
(@groupAdmin, NOW(), NOW(), 'Admin');

-- Assign privileges to groups
INSERT INTO userGroupPrivilege (
    dateCreated,
    dateModified,
    userGroupId,
    userPrivilegeId
) VALUES
(NOW(), NOW(), @groupConsumer, @privReadContent),
(NOW(), NOW(), @groupAdmin, @privReadContent),
(NOW(), NOW(), @groupAdmin, @privCrreateContent);

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
(NOW(), NOW(), 'consumer', 'consumer', 'Consumer', @groupConsumer);