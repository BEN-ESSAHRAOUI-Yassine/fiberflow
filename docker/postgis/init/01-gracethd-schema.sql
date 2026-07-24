CREATE EXTENSION IF NOT EXISTS postgis;

-- TOPOLOGY
CREATE TABLE IF NOT EXISTS t_noeud (
    nd_code VARCHAR(254) PRIMARY KEY,
    nd_nom VARCHAR(254),
    nd_type VARCHAR(50),
    geom GEOMETRY(Point, 2154)
);

-- EQUIPMENT
CREATE TABLE IF NOT EXISTS t_ptech (
    pt_code VARCHAR(254) PRIMARY KEY,
    pt_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
    pt_typephy VARCHAR(50),
    pt_typelog VARCHAR(50),
    pt_etat VARCHAR(50),
    pt_avct VARCHAR(50),
    pt_nature VARCHAR(50),
    pt_prop VARCHAR(254),
    pt_gest VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_ebp (
    bp_code VARCHAR(254) PRIMARY KEY,
    bp_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
    bp_typephy VARCHAR(50),
    bp_typelog VARCHAR(50),
    bp_etat VARCHAR(50),
    bp_prop VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_sitetech (
    st_code VARCHAR(254) PRIMARY KEY,
    st_nd_code VARCHAR(254) REFERENCES t_noeud(nd_code),
    st_typ VARCHAR(50),
    st_etat VARCHAR(50),
    st_prop VARCHAR(254)
);

-- CABLES
CREATE TABLE IF NOT EXISTS t_cable (
    cb_code VARCHAR(254) PRIMARY KEY,
    cb_fo INTEGER,
    cb_typelog VARCHAR(50),
    cb_etat VARCHAR(50),
    cb_prop VARCHAR(254)
);

CREATE TABLE IF NOT EXISTS t_cableline (
    cl_code VARCHAR(254) PRIMARY KEY,
    cl_cb_code VARCHAR(254) REFERENCES t_cable(cb_code),
    geom GEOMETRY(LineString, 2154)
);

-- INFRASTRUCTURE
CREATE TABLE IF NOT EXISTS t_cheminement (
    ch_code VARCHAR(254) PRIMARY KEY,
    ch_typ VARCHAR(50),
    geom GEOMETRY(LineString, 2154)
);

CREATE TABLE IF NOT EXISTS t_conduite (
    cd_code VARCHAR(254) PRIMARY KEY,
    cd_typ VARCHAR(50),
    cd_dia_int DECIMAL(6,2),
    geom GEOMETRY(LineString, 2154)
);

-- ZONES
CREATE TABLE IF NOT EXISTS t_znro (
    zn_code VARCHAR(254) PRIMARY KEY,
    zn_nd_code VARCHAR(254),
    geom GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE IF NOT EXISTS t_zsro (
    zs_code VARCHAR(254) PRIMARY KEY,
    zs_nd_code VARCHAR(254),
    geom GEOMETRY(MultiPolygon, 2154)
);

CREATE TABLE IF NOT EXISTS t_zpbo (
    zp_code VARCHAR(254) PRIMARY KEY,
    zp_nd_code VARCHAR(254),
    geom GEOMETRY(MultiPolygon, 2154)
);
