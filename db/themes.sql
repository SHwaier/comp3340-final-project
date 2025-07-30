-- Insert themes into the database
INSERT INTO themes (theme_name, description)
VALUES 
('Dark', 'A clean quite theme with dark colors'),
('Light', 'A bright and clean theme with light colors'),
('Black Friday', 'A special theme for Black Friday sales with bold colors and promotional elements');

-- set the current theme to the first one
INSERT INTO current_theme (id, theme_id)
VALUES 
(1, 1);