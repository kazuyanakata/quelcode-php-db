-- membersテーブル
CREATE TABLE members (
  id                INTEGER       PRIMARY KEY AUTO_INCREMENT,
  name              VARCHAR(100)  NOT NULL,
  furigana          VARCHAR(100)  NOT NULL,
  mail              VARCHAR(100)  NOT NULL,
  phone_number      VARCHAR(11)   NOT NULL,
  birthday          CHAR(10)      NOT NULL,
  prefecture_id     CHAR(2)       NOT NULL REFERENCES prefectures(id),
  status_id         INTEGER       DEFAULT 1 NOT NULL REFERENCES statuses(id),
  created_at        DATETIME      DEFAULT CURRENT_TIMESTAMP NOT NULL,
  updated_at        TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
) ENGINE = INNODB;

-- prefecturesテーブル
CREATE TABLE prefectures (
  id            CHAR(2)       PRIMARY KEY,
  name          VARCHAR(100)  NOT NULL,
  created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP NOT NULL,
  updated_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
) ENGINE = INNODB;

-- statusesテーブル
CREATE TABLE statuses (
  id            INTEGER       PRIMARY KEY AUTO_INCREMENT,
  name          VARCHAR(100)  NOT NULL,
  created_at    DATETIME      DEFAULT CURRENT_TIMESTAMP NOT NULL,
  updated_at    TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL
) ENGINE = INNODB;

-- prefecturesテーブルに都道府県一覧を挿入
INSERT INTO prefectures
            (id, name)
     VALUES ('01', '北海道'),
            ('02', '青森県'),
            ('03', '岩手県'),
            ('04', '宮城県'),
            ('05', '秋田県'),
            ('06', '山形県'),
            ('07', '福島県'),
            ('08', '茨城県'),
            ('09', '栃木県'),
            ('10', '群馬県'),
            ('11', '埼玉県'),
            ('12', '千葉県'),
            ('13', '東京都'),
            ('14', '神奈川県'),
            ('15', '新潟県'),
            ('16', '富山県'),
            ('17', '石川県'),
            ('18', '福井県'),
            ('19', '山梨県'),
            ('20', '長野県'),
            ('21', '岐阜県'),
            ('22', '静岡県'),
            ('23', '愛知県'),
            ('24', '三重県'),
            ('25', '滋賀県'),
            ('26', '京都府'),
            ('27', '大阪府'),
            ('28', '兵庫県'),
            ('29', '奈良県'),
            ('30', '和歌山県'),
            ('31', '鳥取県'),
            ('32', '島根県'),
            ('33', '岡山県'),
            ('34', '広島県'),
            ('35', '山口県'),
            ('36', '徳島県'),
            ('37', '香川県'),
            ('38', '愛媛県'),
            ('39', '高知県'),
            ('40', '福岡県'),
            ('41', '佐賀県'),
            ('42', '長崎県'),
            ('43', '熊本県'),
            ('44', '大分県'),
            ('45', '宮崎県'),
            ('46', '鹿児島県'),
            ('47', '沖縄県');

-- statusesテーブルにステータス一覧を挿入
INSERT INTO statuses
            (name)
     VALUES ('受付済'),
            ('プレワーク案内済'),
            ('プレワーク中'),
            ('本エントリー済'),
            ('休会中'),
            ('退会');
