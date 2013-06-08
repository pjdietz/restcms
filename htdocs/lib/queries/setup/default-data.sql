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

INSERT INTO status (statusSlug, statusName) VALUES
('draft', 'Draft'),
('published', 'Published'),
('pending', 'Pending Review');