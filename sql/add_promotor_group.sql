-- Add 'Promotor' group to groups table
-- Safe steps to create the group and assign users without causing duplicate-key errors.

-- 1) Check if a 'promotor' group already exists (or if id=4 is used):
SELECT id, name, description FROM `groups` WHERE `name` = 'promotor' OR `id` = 4;

-- 2) Insert the group only if a group with same name doesn't exist.
--    This avoids PRIMARY KEY conflicts and works with auto-increment ids.
INSERT INTO `groups` (`name`, `description`)
SELECT 'promotor', 'Promotor de Ventas'
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `groups` WHERE `name` = 'promotor');

-- 3) Get the group id for use when assigning users (replace <user_id>):
--    This sets a session variable @promotor_gid to the group's id.
SET @promotor_gid = (SELECT id FROM `groups` WHERE `name` = 'promotor' LIMIT 1);

-- 4) Assign an existing user to the Promotor group (replace <user_id> with a real id):
--    Use the resolved @promotor_gid to avoid hardcoding an id.
-- INSERT INTO `users_groups` (`user_id`,`group_id`) VALUES (<user_id>, @promotor_gid);

-- 5) If you rely on the legacy `users.perfil` integer in templates, set it explicitly for that user:
-- UPDATE `users` SET `perfil` = 4 WHERE `id` = <user_id>;

-- Notes:
-- - Prefer using groups (Ion Auth) instead of relying solely on `users.perfil`.
-- - If you explicitly require id=4, first ensure it's free by running:
--     SELECT id FROM `groups` WHERE id = 4;
--   and if free you can run a direct INSERT WITH id, but the safer pattern above is recommended.
