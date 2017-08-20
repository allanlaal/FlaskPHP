# --------------------------------------------------------------------------- #


#
#
#   FlaskPHP
#   --------
#   The core database structure
#
#   (c) Codelab Solutions OÜ <codelab@codelab.ee>
#   Distributed under the MIT License: https://www.flaskphp.com/LICENSE
#
#


# --------------------------------------------------------------------------- #


#
#  DB version
#

DROP TABLE IF EXISTS db_version;
CREATE TABLE db_version
(
  version_tag                  VARCHAR(32) NOT NULL,           #  Version tag
  version_num                  INT UNSIGNED NOT NULL,          #  Version number
  PRIMARY KEY (version_tag)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8;

REPLACE INTO db_version VALUES('flask','1');


# --------------------------------------------------------------------------- #


#
#  OID sequence
#

DROP TABLE IF EXISTS flask_sequence;
CREATE TABLE flask_sequence
(
  sequence_id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,    #  ID
  sequence_objecttype  VARCHAR(32) NOT NULL,                       #  Object type
  PRIMARY KEY (sequence_id)
)
ENGINE=INNODB
AUTO_INCREMENT=10001001
DEFAULT CHARSET=utf8;


# --------------------------------------------------------------------------- #


#
#  Users
#

DROP TABLE IF EXISTS flask_user;
CREATE TABLE flask_user
(
  user_oid                     BIGINT UNSIGNED NOT NULL,       #  User OID

  user_email                   VARCHAR(255) NOT NULL,          #  User e-mail address
  user_name                    VARCHAR(255) NOT NULL,          #  User name
  user_password                VARCHAR(64) NOT NULL,           #  Password

  user_status                  VARCHAR(32) NOT NULL,           #  Status

  user_lastlogin               DATETIME NOT NULL,              #  Last login: timestamp
  user_lastlogin_host          VARCHAR(255) NOT NULL,          #  Last login: host

  add_tstamp                   DATETIME NOT NULL,              #  Add: timestamp
  add_user_oid                 BIGINT UNSIGNED NOT NULL,       #  Add: user OID
  mod_tstamp                   DATETIME NOT NULL,              #  Mod: timestamp
  mod_user_oid                 BIGINT UNSIGNED NOT NULL,       #  Mod: user OID

  PRIMARY KEY (user_oid),
  UNIQUE (user_email)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;

DROP TABLE IF EXISTS flask_user_prop;
CREATE TABLE flask_user_prop
(
  user_oid                     BIGINT UNSIGNED NOT NULL,       #  User OID
  user_prop_name               VARCHAR(255) NOT NULL,          #  Prop name
  user_prop_value              MEDIUMTEXT NOT NULL,            #  Prop value

  PRIMARY KEY (user_oid,user_prop_name)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


#
#  Person roles
#

DROP TABLE IF EXISTS flask_user_role;
CREATE TABLE flask_user_role
(
  user_oid                     BIGINT UNSIGNED NOT NULL,       #  User OID
  user_role                    VARCHAR(255) NOT NULL,          #  Role tag
  UNIQUE (user_oid,user_role)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


# --------------------------------------------------------------------------- #


#
#  Data object modification log
#

DROP TABLE IF EXISTS flask_log;
CREATE TABLE flask_log
(
  ref_oid                      BIGINT UNSIGNED NOT NULL,       #  Reference object OID
  affected_oid                 BIGINT UNSIGNED NOT NULL,       #  OID of object actually changed (if not reference OID)
  user_oid                     BIGINT UNSIGNED NOT NULL,       #  User OID
  log_tstamp                   DATETIME NOT NULL,              #  Timestamp
  log_entry                    MEDIUMTEXT NOT NULL,            #  Log entry/description
  log_data                     MEDIUMTEXT NOT NULL,            #  Log detailed data
  INDEX (ref_oid,log_tstamp)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


# --------------------------------------------------------------------------- #


#
#  Login log
#

DROP TABLE IF EXISTS flask_loginlog;
CREATE TABLE flask_loginlog
(
  user_oid                     BIGINT UNSIGNED NOT NULL,       #  User OID
  loginlog_tstamp              DATETIME NOT NULL,              #  Date/time
  loginlog_status              VARCHAR(32) NOT NULL,           #  Login request status
  loginlog_ip                  VARCHAR(255) NOT NULL,          #  IP
  loginlog_hostname            VARCHAR(255) NOT NULL,          #  Hostname
  loginlog_email               VARCHAR(255) NOT NULL,          #  E-mail address
  loginlog_entry               TEXT NOT NULL,                  #  Log entry
  INDEX (loginlog_tstamp),
  INDEX (loginlog_ip,loginlog_tstamp,loginlog_status)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


# --------------------------------------------------------------------------- #


#
#  DB-flaskd sessions
#

DROP TABLE IF EXISTS flask_session;
CREATE TABLE flask_session
(
  session_id                   VARCHAR(255) NOT NULL,          #  Session ID
  session_tstamp               DATETIME NOT NULL,              #  Session last used timestamp
  session_locked               DATETIME NOT NULL,              #  Session lock timestamp
  session_data                 MEDIUMBLOB NOT NULL,            #  Session data
  PRIMARY KEY (session_id)
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


# --------------------------------------------------------------------------- #


#
#  Page profiler: page request data
#

DROP TABLE IF EXISTS flask_pageprofiler;
CREATE TABLE flask_pageprofiler
(
  pageprofiler_tstamp               DATETIME NOT NULL,         #  Timestamp

  pageprofiler_request_id           VARCHAR(255) NOT NULL,     #  Request ID
  pageprofiler_user_oid             BIGINT UNSIGNED NOT NULL,  #  User OID

  pageprofiler_request_method       VARCHAR(255) NOT NULL,     #  Request method
  pageprofiler_request_uri          VARCHAR(255) NOT NULL,     #  Request URI

  pageprofiler_requesttime          DECIMAL(20,8) NOT NULL,    #  Request time (seconds)
  pageprofiler_dbquerytime          DECIMAL(20,8) NOT NULL,    #  DB query time (seconds)

  pageprofiler_dbquerycnt           INT UNSIGNED NOT NULL,     #  DB query count
  pageprofiler_dbquerycnt_select    INT UNSIGNED NOT NULL,     #  DB query count
  pageprofiler_dbquerycnt_update    INT UNSIGNED NOT NULL,     #  DB query count

  pageprofiler_peakmemoryusage      BIGINT UNSIGNED NOT NULL   #  Peak memory usage
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


#
#  Page profiler: query data
#

DROP TABLE IF EXISTS flask_pageprofiler_query;
CREATE TABLE flask_pageprofiler_query
(
  pageprofiler_query_tstamp         DATETIME NOT NULL,         #  Timestamp
  pageprofiler_query_request_id     VARCHAR(255) NOT NULL,     #  Request ID
  pageprofiler_query_no             INT NOT NULL,              #  Query no during request

  pageprofiler_query_sql            TEXT NOT NULL,             #  SQL
  pageprofiler_query_time           DECIMAL(10,2) NOT NULL,    #  Query time
  pageprofiler_query_affectedrows   BIGINT NOT NULL,           #  Affected rows
  pageprofiler_query_explain        TEXT NOT NULL              #  EXPLAIN output
)
ENGINE=INNODB
DEFAULT CHARSET=utf8
DEFAULT COLLATE=utf8_estonian_ci;


# --------------------------------------------------------------------------- #
