--
-- PostgreSQL database dump
--

\restrict 2KQnzcB7pVzfAvLXmOMOVMT4okqL36Mf4E2I7KP7jaHEbpFHX6GaEecxWIL5Due

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: access_history; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.access_history (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    ip character varying(45) NOT NULL,
    device character varying(255),
    access_time timestamp(0) without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: access_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.access_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: access_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.access_history_id_seq OWNED BY public.access_history.id;


--
-- Name: blocked_emails; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.blocked_emails (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    banned_by bigint NOT NULL,
    email_hash character varying(255) NOT NULL,
    reason text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: blocked_emails_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.blocked_emails_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: blocked_emails_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.blocked_emails_id_seq OWNED BY public.blocked_emails.id;


--
-- Name: blocks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.blocks (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    blocked_profile_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: blocks_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.blocks_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: blocks_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.blocks_id_seq OWNED BY public.blocks.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cities; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cities (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    display_name character varying(255),
    state_id bigint,
    country_code character varying(3) DEFAULT 'BR'::character varying NOT NULL,
    geoname_id bigint,
    latitude numeric(10,7),
    longitude numeric(10,7),
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: cities_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cities_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cities_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cities_id_seq OWNED BY public.cities.id;


--
-- Name: countries; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.countries (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    code character varying(255) NOT NULL,
    abbreviation character varying(5),
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: countries_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.countries_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: countries_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.countries_id_seq OWNED BY public.countries.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: hobbies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.hobbies (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    category character varying(255),
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: hobbies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.hobbies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: hobbies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.hobbies_id_seq OWNED BY public.hobbies.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: languages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.languages (
    id bigint NOT NULL,
    code character varying(5) NOT NULL,
    name character varying(255) NOT NULL,
    active boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: languages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.languages_id_seq OWNED BY public.languages.id;


--
-- Name: likes; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.likes (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    liked_user_id bigint NOT NULL,
    is_like boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: likes_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.likes_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: likes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.likes_id_seq OWNED BY public.likes.id;


--
-- Name: messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.messages (
    id bigint NOT NULL,
    user_match_id bigint NOT NULL,
    sender_id bigint NOT NULL,
    content text NOT NULL,
    read boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.messages_id_seq OWNED BY public.messages.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- Name: profile_hobbies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_hobbies (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    hobby_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: profile_hobbies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_hobbies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_hobbies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_hobbies_id_seq OWNED BY public.profile_hobbies.id;


--
-- Name: profile_hobby_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_hobby_preferences (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    hobby_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: profile_hobby_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_hobby_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_hobby_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_hobby_preferences_id_seq OWNED BY public.profile_hobby_preferences.id;


--
-- Name: profile_language_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_language_preferences (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    language_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: profile_language_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_language_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_language_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_language_preferences_id_seq OWNED BY public.profile_language_preferences.id;


--
-- Name: profile_languages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_languages (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    language_id bigint NOT NULL,
    level character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT profile_languages_level_check CHECK (((level)::text = ANY ((ARRAY['active'::character varying, 'fluent'::character varying, 'intermediate'::character varying, 'basic'::character varying])::text[])))
);


--
-- Name: profile_languages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_languages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_languages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_languages_id_seq OWNED BY public.profile_languages.id;


--
-- Name: profile_photos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_photos (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    path character varying(255) NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    "order" integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: profile_photos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_photos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_photos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_photos_id_seq OWNED BY public.profile_photos.id;


--
-- Name: profile_preferences; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profile_preferences (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    gender character varying(255),
    gender_identity character varying(255),
    sexual_orientation character varying(255),
    purpose character varying(255),
    smoker boolean,
    drinker boolean,
    marital_status character varying(255),
    country_id bigint,
    state_id bigint,
    city_id bigint,
    search_radius_km integer DEFAULT 50 NOT NULL,
    min_age smallint,
    max_age smallint,
    visibility character varying(255) DEFAULT 'public'::character varying NOT NULL,
    allow_global_search boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT profile_preferences_purpose_check CHECK (((purpose)::text = ANY ((ARRAY['serious'::character varying, 'casual'::character varying, 'friendship'::character varying, 'networking'::character varying, 'all'::character varying])::text[]))),
    CONSTRAINT profile_preferences_visibility_check CHECK (((visibility)::text = ANY ((ARRAY['public'::character varying, 'hidden'::character varying, 'matches_only'::character varying])::text[])))
);


--
-- Name: profile_preferences_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profile_preferences_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profile_preferences_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profile_preferences_id_seq OWNED BY public.profile_preferences.id;


--
-- Name: profiles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.profiles (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    nickname character varying(255),
    birth_date date NOT NULL,
    gender character varying(255) NOT NULL,
    gender_identity character varying(255) NOT NULL,
    sexual_orientation character varying(255) NOT NULL,
    purpose character varying(255) NOT NULL,
    profession character varying(255),
    biography text,
    smoker character varying(255),
    drinker character varying(255),
    marital_status character varying(255),
    country_id bigint,
    state_id bigint,
    city_id bigint,
    latitude numeric(10,8),
    longitude numeric(11,8),
    visibility character varying(255) DEFAULT 'public'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT profiles_gender_check CHECK (((gender)::text = ANY ((ARRAY['male'::character varying, 'female'::character varying, 'non_binary'::character varying, 'other'::character varying])::text[]))),
    CONSTRAINT profiles_marital_status_check CHECK (((marital_status)::text = ANY ((ARRAY['single'::character varying, 'married'::character varying, 'divorced'::character varying, 'widowed'::character varying, 'open_relationship'::character varying])::text[]))),
    CONSTRAINT profiles_purpose_check CHECK (((purpose)::text = ANY ((ARRAY['serious'::character varying, 'casual'::character varying, 'friendship'::character varying, 'networking'::character varying, 'all'::character varying])::text[]))),
    CONSTRAINT profiles_visibility_check CHECK (((visibility)::text = ANY ((ARRAY['public'::character varying, 'hidden'::character varying, 'matches_only'::character varying])::text[])))
);


--
-- Name: profiles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.profiles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: profiles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.profiles_id_seq OWNED BY public.profiles.id;


--
-- Name: promotional_contracts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.promotional_contracts (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    offer_name character varying(255) NOT NULL,
    accepted_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    start_at timestamp(0) without time zone,
    end_at timestamp(0) without time zone,
    complying_with_rules boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: promotional_contracts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.promotional_contracts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: promotional_contracts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.promotional_contracts_id_seq OWNED BY public.promotional_contracts.id;


--
-- Name: refresh_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.refresh_tokens (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    token_hash character varying(64) NOT NULL,
    token_family character varying(32) NOT NULL,
    scopes json,
    expires_at timestamp(0) without time zone NOT NULL,
    revoked_at timestamp(0) without time zone,
    ip_address inet,
    user_agent text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.refresh_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: refresh_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.refresh_tokens_id_seq OWNED BY public.refresh_tokens.id;


--
-- Name: reports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reports (
    id bigint NOT NULL,
    reporter_id bigint NOT NULL,
    reported_id bigint NOT NULL,
    reason character varying(255) NOT NULL,
    description text,
    status character varying(255) DEFAULT 'pending'::character varying NOT NULL,
    analyzed_by bigint,
    analyzed_at timestamp(0) without time zone,
    analysis_notes text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT reports_reason_check CHECK (((reason)::text = ANY ((ARRAY['harassment'::character varying, 'disrespect'::character varying, 'fake_profile'::character varying, 'other'::character varying])::text[]))),
    CONSTRAINT reports_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'analyzed'::character varying, 'resolved'::character varying])::text[])))
);


--
-- Name: reports_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reports_id_seq OWNED BY public.reports.id;


--
-- Name: second_chances; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.second_chances (
    id bigint NOT NULL,
    profile_id bigint NOT NULL,
    like_id bigint NOT NULL,
    used_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: second_chances_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.second_chances_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: second_chances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.second_chances_id_seq OWNED BY public.second_chances.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: shorts; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.shorts (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    tipo character varying(255) NOT NULL,
    conteudo text NOT NULL,
    posicao smallint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT shorts_tipo_check CHECK (((tipo)::text = ANY ((ARRAY['q'::character varying, 'r'::character varying])::text[])))
);


--
-- Name: shorts_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.shorts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: shorts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.shorts_id_seq OWNED BY public.shorts.id;


--
-- Name: states; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.states (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    code character varying(5),
    uf character varying(5),
    active boolean DEFAULT true NOT NULL,
    country_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: states_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.states_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: states_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.states_id_seq OWNED BY public.states.id;


--
-- Name: user_matches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.user_matches (
    id bigint NOT NULL,
    user_one_id bigint NOT NULL,
    user_two_id bigint NOT NULL,
    is_favorite boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: user_matches_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.user_matches_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: user_matches_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.user_matches_id_seq OWNED BY public.user_matches.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    uuid character(26),
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    ativo boolean DEFAULT true NOT NULL,
    nivel_acesso integer DEFAULT 0 NOT NULL,
    two_factor_secret text,
    two_factor_recovery_codes text,
    two_factor_confirmed_at timestamp(0) without time zone,
    two_factor_enabled boolean DEFAULT false NOT NULL,
    assinatura_id character varying(255),
    reputacao integer DEFAULT 0 NOT NULL,
    premium_expira_em timestamp(0) without time zone,
    ultima_interacao_at timestamp(0) without time zone,
    ultima_conversa_at timestamp(0) without time zone,
    ultimo_login_em timestamp(0) without time zone,
    ultimo_ip character varying(45),
    last_seen timestamp(0) without time zone,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: access_history id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.access_history ALTER COLUMN id SET DEFAULT nextval('public.access_history_id_seq'::regclass);


--
-- Name: blocked_emails id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_emails ALTER COLUMN id SET DEFAULT nextval('public.blocked_emails_id_seq'::regclass);


--
-- Name: blocks id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocks ALTER COLUMN id SET DEFAULT nextval('public.blocks_id_seq'::regclass);


--
-- Name: cities id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities ALTER COLUMN id SET DEFAULT nextval('public.cities_id_seq'::regclass);


--
-- Name: countries id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries ALTER COLUMN id SET DEFAULT nextval('public.countries_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: hobbies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.hobbies ALTER COLUMN id SET DEFAULT nextval('public.hobbies_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: languages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.languages ALTER COLUMN id SET DEFAULT nextval('public.languages_id_seq'::regclass);


--
-- Name: likes id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.likes ALTER COLUMN id SET DEFAULT nextval('public.likes_id_seq'::regclass);


--
-- Name: messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages ALTER COLUMN id SET DEFAULT nextval('public.messages_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- Name: profile_hobbies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobbies ALTER COLUMN id SET DEFAULT nextval('public.profile_hobbies_id_seq'::regclass);


--
-- Name: profile_hobby_preferences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobby_preferences ALTER COLUMN id SET DEFAULT nextval('public.profile_hobby_preferences_id_seq'::regclass);


--
-- Name: profile_language_preferences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_language_preferences ALTER COLUMN id SET DEFAULT nextval('public.profile_language_preferences_id_seq'::regclass);


--
-- Name: profile_languages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_languages ALTER COLUMN id SET DEFAULT nextval('public.profile_languages_id_seq'::regclass);


--
-- Name: profile_photos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_photos ALTER COLUMN id SET DEFAULT nextval('public.profile_photos_id_seq'::regclass);


--
-- Name: profile_preferences id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences ALTER COLUMN id SET DEFAULT nextval('public.profile_preferences_id_seq'::regclass);


--
-- Name: profiles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles ALTER COLUMN id SET DEFAULT nextval('public.profiles_id_seq'::regclass);


--
-- Name: promotional_contracts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.promotional_contracts ALTER COLUMN id SET DEFAULT nextval('public.promotional_contracts_id_seq'::regclass);


--
-- Name: refresh_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refresh_tokens ALTER COLUMN id SET DEFAULT nextval('public.refresh_tokens_id_seq'::regclass);


--
-- Name: reports id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports ALTER COLUMN id SET DEFAULT nextval('public.reports_id_seq'::regclass);


--
-- Name: second_chances id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.second_chances ALTER COLUMN id SET DEFAULT nextval('public.second_chances_id_seq'::regclass);


--
-- Name: shorts id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.shorts ALTER COLUMN id SET DEFAULT nextval('public.shorts_id_seq'::regclass);


--
-- Name: states id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.states ALTER COLUMN id SET DEFAULT nextval('public.states_id_seq'::regclass);


--
-- Name: user_matches id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_matches ALTER COLUMN id SET DEFAULT nextval('public.user_matches_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: access_history access_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.access_history
    ADD CONSTRAINT access_history_pkey PRIMARY KEY (id);


--
-- Name: blocked_emails blocked_emails_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_emails
    ADD CONSTRAINT blocked_emails_pkey PRIMARY KEY (id);


--
-- Name: blocks blocks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocks
    ADD CONSTRAINT blocks_pkey PRIMARY KEY (id);


--
-- Name: blocks blocks_profile_id_blocked_profile_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocks
    ADD CONSTRAINT blocks_profile_id_blocked_profile_id_unique UNIQUE (profile_id, blocked_profile_id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: cities cities_geoname_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities
    ADD CONSTRAINT cities_geoname_id_unique UNIQUE (geoname_id);


--
-- Name: cities cities_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cities
    ADD CONSTRAINT cities_pkey PRIMARY KEY (id);


--
-- Name: countries countries_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_code_unique UNIQUE (code);


--
-- Name: countries countries_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_name_unique UNIQUE (name);


--
-- Name: countries countries_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.countries
    ADD CONSTRAINT countries_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: hobbies hobbies_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.hobbies
    ADD CONSTRAINT hobbies_name_unique UNIQUE (name);


--
-- Name: hobbies hobbies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.hobbies
    ADD CONSTRAINT hobbies_pkey PRIMARY KEY (id);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: languages languages_code_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_code_unique UNIQUE (code);


--
-- Name: languages languages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.languages
    ADD CONSTRAINT languages_pkey PRIMARY KEY (id);


--
-- Name: likes likes_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.likes
    ADD CONSTRAINT likes_pkey PRIMARY KEY (id);


--
-- Name: likes likes_user_id_liked_user_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.likes
    ADD CONSTRAINT likes_user_id_liked_user_id_unique UNIQUE (user_id, liked_user_id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- Name: profile_hobbies profile_hobbies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobbies
    ADD CONSTRAINT profile_hobbies_pkey PRIMARY KEY (id);


--
-- Name: profile_hobbies profile_hobbies_profile_id_hobby_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobbies
    ADD CONSTRAINT profile_hobbies_profile_id_hobby_id_unique UNIQUE (profile_id, hobby_id);


--
-- Name: profile_hobby_preferences profile_hobby_pref_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobby_preferences
    ADD CONSTRAINT profile_hobby_pref_unique UNIQUE (profile_id, hobby_id);


--
-- Name: profile_hobby_preferences profile_hobby_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobby_preferences
    ADD CONSTRAINT profile_hobby_preferences_pkey PRIMARY KEY (id);


--
-- Name: profile_language_preferences profile_language_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_language_preferences
    ADD CONSTRAINT profile_language_preferences_pkey PRIMARY KEY (id);


--
-- Name: profile_language_preferences profile_language_preferences_profile_id_language_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_language_preferences
    ADD CONSTRAINT profile_language_preferences_profile_id_language_id_unique UNIQUE (profile_id, language_id);


--
-- Name: profile_languages profile_languages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_languages
    ADD CONSTRAINT profile_languages_pkey PRIMARY KEY (id);


--
-- Name: profile_languages profile_languages_profile_id_language_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_languages
    ADD CONSTRAINT profile_languages_profile_id_language_id_unique UNIQUE (profile_id, language_id);


--
-- Name: profile_photos profile_photos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_photos
    ADD CONSTRAINT profile_photos_pkey PRIMARY KEY (id);


--
-- Name: profile_preferences profile_preferences_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_pkey PRIMARY KEY (id);


--
-- Name: profile_preferences profile_preferences_profile_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_profile_id_unique UNIQUE (profile_id);


--
-- Name: profiles profiles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles
    ADD CONSTRAINT profiles_pkey PRIMARY KEY (id);


--
-- Name: promotional_contracts promotional_contracts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.promotional_contracts
    ADD CONSTRAINT promotional_contracts_pkey PRIMARY KEY (id);


--
-- Name: refresh_tokens refresh_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refresh_tokens
    ADD CONSTRAINT refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: reports reports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_pkey PRIMARY KEY (id);


--
-- Name: second_chances second_chances_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.second_chances
    ADD CONSTRAINT second_chances_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: shorts shorts_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.shorts
    ADD CONSTRAINT shorts_pkey PRIMARY KEY (id);


--
-- Name: states states_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.states
    ADD CONSTRAINT states_pkey PRIMARY KEY (id);


--
-- Name: user_matches user_matches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_matches
    ADD CONSTRAINT user_matches_pkey PRIMARY KEY (id);


--
-- Name: user_matches user_matches_user_one_id_user_two_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_matches
    ADD CONSTRAINT user_matches_user_one_id_user_two_id_unique UNIQUE (user_one_id, user_two_id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: users users_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_uuid_unique UNIQUE (uuid);


--
-- Name: blocked_emails_email_hash_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX blocked_emails_email_hash_index ON public.blocked_emails USING btree (email_hash);


--
-- Name: blocks_blocked_profile_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX blocks_blocked_profile_id_index ON public.blocks USING btree (blocked_profile_id);


--
-- Name: blocks_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX blocks_created_at_index ON public.blocks USING btree (created_at);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: cities_country_code_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cities_country_code_index ON public.cities USING btree (country_code);


--
-- Name: cities_latitude_longitude_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cities_latitude_longitude_index ON public.cities USING btree (latitude, longitude);


--
-- Name: cities_state_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cities_state_id_index ON public.cities USING btree (state_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: refresh_tokens_token_family_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refresh_tokens_token_family_index ON public.refresh_tokens USING btree (token_family);


--
-- Name: refresh_tokens_token_family_revoked_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refresh_tokens_token_family_revoked_at_index ON public.refresh_tokens USING btree (token_family, revoked_at);


--
-- Name: refresh_tokens_token_hash_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refresh_tokens_token_hash_index ON public.refresh_tokens USING btree (token_hash);


--
-- Name: refresh_tokens_user_id_revoked_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX refresh_tokens_user_id_revoked_at_index ON public.refresh_tokens USING btree (user_id, revoked_at);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: user_matches_user_one_id_user_two_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX user_matches_user_one_id_user_two_id_index ON public.user_matches USING btree (user_one_id, user_two_id);


--
-- Name: users_reputacao_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_reputacao_index ON public.users USING btree (reputacao);


--
-- Name: users_ultima_conversa_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_ultima_conversa_at_index ON public.users USING btree (ultima_conversa_at);


--
-- Name: users_ultima_interacao_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX users_ultima_interacao_at_index ON public.users USING btree (ultima_interacao_at);


--
-- Name: access_history access_history_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.access_history
    ADD CONSTRAINT access_history_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: blocked_emails blocked_emails_banned_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_emails
    ADD CONSTRAINT blocked_emails_banned_by_foreign FOREIGN KEY (banned_by) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: blocked_emails blocked_emails_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocked_emails
    ADD CONSTRAINT blocked_emails_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: blocks blocks_blocked_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocks
    ADD CONSTRAINT blocks_blocked_profile_id_foreign FOREIGN KEY (blocked_profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: blocks blocks_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.blocks
    ADD CONSTRAINT blocks_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: likes likes_liked_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.likes
    ADD CONSTRAINT likes_liked_user_id_foreign FOREIGN KEY (liked_user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: likes likes_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.likes
    ADD CONSTRAINT likes_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: messages messages_sender_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_sender_id_foreign FOREIGN KEY (sender_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: messages messages_user_match_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_user_match_id_foreign FOREIGN KEY (user_match_id) REFERENCES public.user_matches(id) ON DELETE CASCADE;


--
-- Name: profile_hobbies profile_hobbies_hobby_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobbies
    ADD CONSTRAINT profile_hobbies_hobby_id_foreign FOREIGN KEY (hobby_id) REFERENCES public.hobbies(id) ON DELETE CASCADE;


--
-- Name: profile_hobbies profile_hobbies_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobbies
    ADD CONSTRAINT profile_hobbies_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: profile_hobby_preferences profile_hobby_preferences_hobby_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobby_preferences
    ADD CONSTRAINT profile_hobby_preferences_hobby_id_foreign FOREIGN KEY (hobby_id) REFERENCES public.hobbies(id) ON DELETE CASCADE;


--
-- Name: profile_hobby_preferences profile_hobby_preferences_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_hobby_preferences
    ADD CONSTRAINT profile_hobby_preferences_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: profile_language_preferences profile_language_preferences_language_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_language_preferences
    ADD CONSTRAINT profile_language_preferences_language_id_foreign FOREIGN KEY (language_id) REFERENCES public.languages(id) ON DELETE CASCADE;


--
-- Name: profile_language_preferences profile_language_preferences_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_language_preferences
    ADD CONSTRAINT profile_language_preferences_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: profile_languages profile_languages_language_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_languages
    ADD CONSTRAINT profile_languages_language_id_foreign FOREIGN KEY (language_id) REFERENCES public.languages(id) ON DELETE CASCADE;


--
-- Name: profile_languages profile_languages_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_languages
    ADD CONSTRAINT profile_languages_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: profile_photos profile_photos_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_photos
    ADD CONSTRAINT profile_photos_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: profile_preferences profile_preferences_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id) ON DELETE SET NULL;


--
-- Name: profile_preferences profile_preferences_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE SET NULL;


--
-- Name: profile_preferences profile_preferences_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: profile_preferences profile_preferences_state_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profile_preferences
    ADD CONSTRAINT profile_preferences_state_id_foreign FOREIGN KEY (state_id) REFERENCES public.states(id) ON DELETE SET NULL;


--
-- Name: profiles profiles_city_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles
    ADD CONSTRAINT profiles_city_id_foreign FOREIGN KEY (city_id) REFERENCES public.cities(id);


--
-- Name: profiles profiles_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles
    ADD CONSTRAINT profiles_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id);


--
-- Name: profiles profiles_state_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles
    ADD CONSTRAINT profiles_state_id_foreign FOREIGN KEY (state_id) REFERENCES public.states(id);


--
-- Name: profiles profiles_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.profiles
    ADD CONSTRAINT profiles_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: promotional_contracts promotional_contracts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.promotional_contracts
    ADD CONSTRAINT promotional_contracts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: refresh_tokens refresh_tokens_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.refresh_tokens
    ADD CONSTRAINT refresh_tokens_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: reports reports_analyzed_by_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_analyzed_by_foreign FOREIGN KEY (analyzed_by) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: reports reports_reported_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_reported_id_foreign FOREIGN KEY (reported_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: reports reports_reporter_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_reporter_id_foreign FOREIGN KEY (reporter_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: second_chances second_chances_like_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.second_chances
    ADD CONSTRAINT second_chances_like_id_foreign FOREIGN KEY (like_id) REFERENCES public.likes(id) ON DELETE CASCADE;


--
-- Name: second_chances second_chances_profile_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.second_chances
    ADD CONSTRAINT second_chances_profile_id_foreign FOREIGN KEY (profile_id) REFERENCES public.profiles(id) ON DELETE CASCADE;


--
-- Name: shorts shorts_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.shorts
    ADD CONSTRAINT shorts_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: states states_country_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.states
    ADD CONSTRAINT states_country_id_foreign FOREIGN KEY (country_id) REFERENCES public.countries(id) ON DELETE CASCADE;


--
-- Name: user_matches user_matches_user_one_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_matches
    ADD CONSTRAINT user_matches_user_one_id_foreign FOREIGN KEY (user_one_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_matches user_matches_user_two_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.user_matches
    ADD CONSTRAINT user_matches_user_two_id_foreign FOREIGN KEY (user_two_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict 2KQnzcB7pVzfAvLXmOMOVMT4okqL36Mf4E2I7KP7jaHEbpFHX6GaEecxWIL5Due

--
-- PostgreSQL database dump
--

\restrict t9sfVBTe6vQd2ygV95u3MsbPS2I6QJj4Wc99j3lopdbSo75XViDAuxwCSL2XO4P

-- Dumped from database version 18.3
-- Dumped by pg_dump version 18.3

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_05_04_000001_create_refresh_tokens_table	1
5	2026_02_18_132146_create_personal_access_tokens_table	1
6	2026_02_18_133547_create_user_matches_table	1
7	2026_02_18_185100_create_blocked_emails_table	1
8	2026_02_18_185335_create_shorts_table	1
9	2026_02_18_194156_create_countries_table	1
10	2026_02_18_194221_create_states_table	1
11	2026_02_18_194229_create_cities_table	1
12	2026_02_18_200956_create_access_history_table	1
13	2026_02_19_132924_create_profiles_table	1
14	2026_02_19_135627_create_profile_photos_table	1
15	2026_02_19_140142_create_profile_preferences_table	1
16	2026_02_19_180954_create_messages_table	1
17	2026_02_19_181907_create_blocks_table	1
18	2026_02_19_181926_create_reports_table	1
19	2026_02_19_205544_create_promotional_contracts_table	1
20	2026_02_21_112926_create_languages_table	1
21	2026_02_21_112945_create_profile_languages_table	1
22	2026_02_21_113002_create_profile_language_preferences_table	1
23	2026_02_21_121838_create_hobbies_table	1
24	2026_02_21_121905_create_profile_hobbies_table	1
25	2026_02_21_181359_create_profile_hobby_preferences_table	1
26	2026_02_23_133408_create_likes_table	1
27	2026_02_23_135714_create_second_chances_table	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 27, true);


--
-- PostgreSQL database dump complete
--

\unrestrict t9sfVBTe6vQd2ygV95u3MsbPS2I6QJj4Wc99j3lopdbSo75XViDAuxwCSL2XO4P

