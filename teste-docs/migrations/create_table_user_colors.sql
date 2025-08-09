CREATE TABLE IF NOT EXISTS user_colors (
        user_id INTEGER NOT NULL,
        color_id INTEGER NOT NULL,
        PRIMARY KEY(user_id, color_id),
        FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY(color_id) REFERENCES colors(id) ON DELETE CASCADE
)