INSERT INTO user (
    dateCreated,
    dateModified,
    username,
    passwordHash,
    displayName,
    isAdmin
) VALUES (
    NOW(),
    NOW(),
    'admin',
    'admin',
    'Administrator',
    1
);

