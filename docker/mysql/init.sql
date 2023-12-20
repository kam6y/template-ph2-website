DROP DATABASE IF EXISTS posse;
CREATE DATABASE posse;
USE posse;

CREATE TABLE questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  content VARCHAR(255) NOT NULL,
  image VARCHAR(255) NOT NULL,
  supplement VARCHAR(255)
) CHARSET=utf8;

-- データの追加
INSERT INTO questions (content, image, supplement) VALUES
('日本のIT人材が2030年には最大どれくらい不足すると言われているでしょうか？', 'img-quiz01.png', '経済産業省 2019年3月 － IT 人材需給に関する調査'),
('既存業界のビジネスと、先進的なテクノロジーを結びつけて生まれた、新しいビジネスのことをなんと言うでしょう？', 'img-quiz02.png', NULL),
('IoTとは何の略でしょう？', 'img-quiz03.png', NULL),
('サイバー空間とフィジカル空間を高度に融合させたシステムにより、経済発展と社会的課題の解決を両立する、人間中心の社会のことをなんと言うでしょう？', 'img-quiz04.png', 'Society5.0 - 科学技術政策 - 内閣府'),
('イギリスのコンピューター科学者であるギャビン・ウッド氏が提唱した、ブロックチェーン技術を活用した「次世代分散型インターネット」のことをなんと言うでしょう？', 'img-quiz05.png', NULL),
('先進テクノロジー活用企業と出遅れた企業の収益性の差はどれくらいあると言われているでしょうか？', 'img-quiz06.png', 'Accenture Technology Vision 2021');

-- 選択肢テーブルの作成
CREATE TABLE choices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  valid TINYINT(1) NOT NULL,
  FOREIGN KEY (question_id) REFERENCES questions(id)
) CHARSET=utf8;

-- データの追加
INSERT INTO choices (question_id, name, valid) VALUES
(1, '約28万人', 0), 
(1, '約79万人', 1), 
(1, '約183万人', 0), 
(2, 'INTECH', 0), 
(2, 'BIZZTECH', 0), 
(2, 'X-TECH', 1), 
(3, 'Internet of Things', 1), 
(3, 'Integrate into Technology', 0), 
(3, 'Information on Tool', 0), 
(4, 'Society 5.0', 1), 
(4, 'CyPhy', 0), 
(4, 'SDGs', 0), 
(5, 'Web3.0', 1), 
(5, 'NFT', 0), 
(5, 'メタバース', 0), 
(6, '約2倍', 0), 
(6, '約5倍', 1), 
(6, '約11倍', 0);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255),
  email VARCHAR(255),
  password VARCHAR(255)
) CHARSET=utf8;

INSERT INTO users (name, email, password) values ("管理者1", "admin@example.com", "$2y$10$csAFREneXMq1sdnuvOrFWe.ZW0kDM3Qigy1S0bhFJ3hhc6fgpMEIy");

DROP TABLE IF EXISTS user_invitations;
CREATE TABLE user_invitations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  -- expired_at DATETIME,
  invited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  activated_at DATETIME,
  token VARCHAR(255),
  FOREIGN KEY (user_id) REFERENCES users(id)
) CHARSET=utf8;

insert into user_invitations (user_id, invited_at, activated_at, token) values (1, DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY), CURRENT_DATE, "token");