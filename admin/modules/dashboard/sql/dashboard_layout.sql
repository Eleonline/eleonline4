CREATE TABLE dashboard_layout (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL,
  card_id VARCHAR(50) NOT NULL,
  posizione INT NOT NULL,
  visibile TINYINT(1) NOT NULL,
  side_panel_open TINYINT(1) NOT NULL
);
