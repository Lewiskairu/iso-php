-- Hero Slides table
-- Run this once in your PostgreSQL database to enable the hero slider.
-- After running, go to Admin → Modules → Hero Slides to add your first slides.

CREATE TABLE IF NOT EXISTS hero_slides (
    id          SERIAL PRIMARY KEY,
    title       TEXT        NOT NULL,
    subtitle    TEXT,
    description TEXT,
    image_url   TEXT,
    cta_text    TEXT,
    cta_link    TEXT,
    sort_order  INT         NOT NULL DEFAULT 0,
    active      BOOLEAN     NOT NULL DEFAULT TRUE,
    created_at  TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

-- Optional: seed with a sample slide for testing
-- INSERT INTO hero_slides (title, subtitle, description, cta_text, cta_link, sort_order, active)
-- VALUES (
--     'ISO Compliance Made Simple',
--     'Platform',
--     'Your all-in-one workspace for assessments, certifications and marketplace products.',
--     'Get Started',
--     '/certification/request',
--     0,
--     TRUE
-- );
