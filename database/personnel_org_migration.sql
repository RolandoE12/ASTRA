ALTER TABLE personnel ADD COLUMN parent_id INT NULL DEFAULT NULL AFTER department_id;
ALTER TABLE personnel ADD COLUMN sort_order INT NOT NULL DEFAULT 0 AFTER image;
ALTER TABLE personnel ADD INDEX idx_personnel_parent (parent_id);
ALTER TABLE personnel ADD CONSTRAINT fk_personnel_parent FOREIGN KEY (parent_id) REFERENCES personnel(id) ON DELETE SET NULL;
