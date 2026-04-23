--
-- PostgreSQL database dump
--

\restrict CRoC50fx5DdldYKhvzGdbyIHsXXYUS9FhA8P91Hdffc40mStwk7pWArR1vBT5oh

-- Dumped from database version 18.1 (Debian 18.1-2)
-- Dumped by pg_dump version 18.1 (Debian 18.1-2)

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
-- Name: question_type; Type: TYPE; Schema: public; Owner: iso_app
--

CREATE TYPE public.question_type AS ENUM (
    'YES_NO',
    'SCALE',
    'MULTIPLE_CHOICE',
    'TEXT'
);


ALTER TYPE public.question_type OWNER TO iso_app;

--
-- Name: user_role; Type: TYPE; Schema: public; Owner: iso_app
--

CREATE TYPE public.user_role AS ENUM (
    'USER',
    'ADMIN',
    'PARTNER'
);


ALTER TYPE public.user_role OWNER TO iso_app;

--
-- Name: update_updated_at_column(); Type: FUNCTION; Schema: public; Owner: iso_app
--

CREATE FUNCTION public.update_updated_at_column() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW."updatedAt" = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;


ALTER FUNCTION public.update_updated_at_column() OWNER TO iso_app;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: about_us; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.about_us (
    id integer NOT NULL,
    tagline character varying(255),
    vision text,
    mission text,
    services text,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.about_us OWNER TO postgres;

--
-- Name: about_us_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.about_us_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.about_us_id_seq OWNER TO postgres;

--
-- Name: about_us_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.about_us_id_seq OWNED BY public.about_us.id;


--
-- Name: accounts; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.accounts (
    id character varying(255) NOT NULL,
    "userId" character varying(255) CONSTRAINT accounts_userid_not_null NOT NULL,
    type character varying(255) NOT NULL,
    provider character varying(255) NOT NULL,
    "providerAccountId" character varying(255) CONSTRAINT accounts_provideraccountid_not_null NOT NULL,
    refresh_token text,
    access_token text,
    expires_at integer,
    token_type character varying(255),
    scope character varying(255),
    id_token text,
    session_state character varying(255)
);


ALTER TABLE public.accounts OWNER TO iso_app;

--
-- Name: answers; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.answers (
    id character varying(255) NOT NULL,
    "assessmentId" character varying(255) CONSTRAINT answers_assessmentid_not_null NOT NULL,
    "questionId" character varying(255) CONSTRAINT answers_questionid_not_null NOT NULL,
    value character varying(255) NOT NULL,
    "textValue" text,
    score numeric(10,2),
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT answers_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT answers_updatedat_not_null NOT NULL
);


ALTER TABLE public.answers OWNER TO iso_app;

--
-- Name: assessments; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.assessments (
    id character varying(255) NOT NULL,
    "userId" character varying(255) CONSTRAINT assessments_userid_not_null NOT NULL,
    "isoStandardId" character varying(255) CONSTRAINT assessments_isostandardid_not_null NOT NULL,
    title character varying(255),
    status character varying(50) DEFAULT 'IN_PROGRESS'::character varying NOT NULL,
    "complianceScore" numeric(5,2),
    "maturityLevel" character varying(50),
    "completedAt" timestamp without time zone,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT assessments_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT assessments_updatedat_not_null NOT NULL
);


ALTER TABLE public.assessments OWNER TO iso_app;

--
-- Name: categories; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.categories (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    description text,
    "imageUrl" character varying(500),
    "parentId" character varying(255),
    "order" integer DEFAULT 0 NOT NULL,
    active boolean DEFAULT true NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    parentid character varying(64)
);


ALTER TABLE public.categories OWNER TO iso_app;

--
-- Name: certification_requests; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.certification_requests (
    id text NOT NULL,
    "companyName" text NOT NULL,
    "contactName" text NOT NULL,
    "contactEmail" text NOT NULL,
    "contactPhone" text,
    "companySize" text,
    "currentStatus" text,
    requirements text,
    status text DEFAULT 'NEW'::text,
    "userId" text,
    "createdAt" timestamp(3) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    "updatedAt" timestamp(3) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    documents jsonb DEFAULT '[]'::jsonb
);


ALTER TABLE public.certification_requests OWNER TO iso_app;

--
-- Name: clauses; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.clauses (
    id character varying NOT NULL,
    "isoStandardId" character varying(255) CONSTRAINT clauses_isostandardid_not_null NOT NULL,
    number character varying(50) NOT NULL,
    title character varying(255) NOT NULL,
    description text,
    weight numeric(10,2) DEFAULT 1.0 NOT NULL,
    "order" integer NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT clauses_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT clauses_updatedat_not_null NOT NULL
);


ALTER TABLE public.clauses OWNER TO iso_app;

--
-- Name: iso_settings; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.iso_settings (
    id integer NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    standard_id integer
);


ALTER TABLE public.iso_settings OWNER TO postgres;

--
-- Name: iso_settings_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.iso_settings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.iso_settings_id_seq OWNER TO postgres;

--
-- Name: iso_settings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.iso_settings_id_seq OWNED BY public.iso_settings.id;


--
-- Name: iso_standards; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.iso_standards (
    id character varying(255) NOT NULL,
    code character varying(50) NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    year integer,
    active boolean DEFAULT true NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT iso_standards_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT iso_standards_updatedat_not_null NOT NULL
);


ALTER TABLE public.iso_standards OWNER TO iso_app;

--
-- Name: leads; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.leads (
    id character varying(255) NOT NULL,
    "userId" character varying(255),
    "isoStandardId" character varying(255) CONSTRAINT leads_isostandardid_not_null NOT NULL,
    "companyName" character varying(255) CONSTRAINT leads_companyname_not_null NOT NULL,
    "contactName" character varying(255) CONSTRAINT leads_contactname_not_null NOT NULL,
    "contactEmail" character varying(255) CONSTRAINT leads_contactemail_not_null NOT NULL,
    "contactPhone" character varying(50),
    "companySize" character varying(50),
    "currentStatus" text,
    requirements text,
    status character varying(50) DEFAULT 'NEW'::character varying NOT NULL,
    "assignedPartnerId" character varying(255),
    notes text,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT leads_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT leads_updatedat_not_null NOT NULL,
    "lastMessageAt" timestamp without time zone,
    "unreadMessagesCount" integer DEFAULT 0 NOT NULL,
    "companyLogo" character varying(255)
);


ALTER TABLE public.leads OWNER TO iso_app;

--
-- Name: messages; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.messages (
    id character varying(255) NOT NULL,
    "leadId" character varying(255) NOT NULL,
    "senderId" character varying(255) NOT NULL,
    "senderRole" character varying(50) NOT NULL,
    message text NOT NULL,
    "isInternal" boolean DEFAULT false NOT NULL,
    "readAt" timestamp without time zone,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.messages OWNER TO iso_app;

--
-- Name: nominations; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.nominations (
    id character varying(255) NOT NULL,
    "nominatorName" character varying(255) NOT NULL,
    "nominatorEmail" character varying(255) NOT NULL,
    "nomineeName" character varying(255) NOT NULL,
    "nomineeEmail" character varying(255),
    "nominationType" character varying(50) NOT NULL,
    reason text NOT NULL,
    status character varying(50) DEFAULT 'NEW'::character varying NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.nominations OWNER TO iso_app;

--
-- Name: order_items; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.order_items (
    id character varying(255) NOT NULL,
    "orderId" character varying(255) CONSTRAINT order_items_orderid_not_null NOT NULL,
    "productId" character varying(255) CONSTRAINT order_items_productid_not_null NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    price numeric(10,2) NOT NULL
);


ALTER TABLE public.order_items OWNER TO iso_app;

--
-- Name: orders; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.orders (
    id character varying(255) NOT NULL,
    "userId" character varying(255) CONSTRAINT orders_userid_not_null NOT NULL,
    "stripePaymentId" character varying(255),
    total numeric(10,2) NOT NULL,
    currency character varying(10) DEFAULT 'USD'::character varying NOT NULL,
    status character varying(50) DEFAULT 'PENDING'::character varying NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT orders_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT orders_updatedat_not_null NOT NULL
);


ALTER TABLE public.orders OWNER TO iso_app;

--
-- Name: partners; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.partners (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    url character varying(512),
    logo_url character varying(512) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.partners OWNER TO postgres;

--
-- Name: partners_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.partners_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.partners_id_seq OWNER TO postgres;

--
-- Name: partners_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.partners_id_seq OWNED BY public.partners.id;


--
-- Name: pending_orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.pending_orders (
    id character varying(255) NOT NULL,
    checkoutrequestid character varying(255) NOT NULL,
    merchantrequestid character varying(255) NOT NULL,
    userid character varying(255) NOT NULL,
    orderitems jsonb NOT NULL,
    total numeric(10,2) NOT NULL,
    currency character varying(10) DEFAULT 'KES'::character varying NOT NULL,
    phonenumber character varying(20) NOT NULL,
    status character varying(50) DEFAULT 'PENDING'::character varying NOT NULL,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    expiresat timestamp without time zone NOT NULL
);


ALTER TABLE public.pending_orders OWNER TO postgres;

--
-- Name: product_category_recommendations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_category_recommendations (
    id integer NOT NULL,
    product_id character varying(255) NOT NULL,
    category_id character varying(255) NOT NULL,
    sort_order integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.product_category_recommendations OWNER TO postgres;

--
-- Name: product_category_recommendations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_category_recommendations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_category_recommendations_id_seq OWNER TO postgres;

--
-- Name: product_category_recommendations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_category_recommendations_id_seq OWNED BY public.product_category_recommendations.id;


--
-- Name: product_images; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_images (
    id integer NOT NULL,
    product_id character varying(255) NOT NULL,
    image_url text NOT NULL,
    sort_order integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.product_images OWNER TO postgres;

--
-- Name: product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_images_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_images_id_seq OWNER TO postgres;

--
-- Name: product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_images_id_seq OWNED BY public.product_images.id;


--
-- Name: product_recommendations; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.product_recommendations (
    id integer NOT NULL,
    product_id character varying(255) NOT NULL,
    recommended_product_id character varying(255) NOT NULL,
    sort_order integer DEFAULT 0,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.product_recommendations OWNER TO postgres;

--
-- Name: product_recommendations_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.product_recommendations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_recommendations_id_seq OWNER TO postgres;

--
-- Name: product_recommendations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.product_recommendations_id_seq OWNED BY public.product_recommendations.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.products (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    description text NOT NULL,
    price numeric(10,2) NOT NULL,
    currency character varying(10) DEFAULT 'USD'::character varying NOT NULL,
    sku character varying(100) NOT NULL,
    type character varying(50) NOT NULL,
    "fileUrl" character varying(500),
    imageurl character varying(500),
    active boolean DEFAULT true NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT products_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT products_updatedat_not_null NOT NULL,
    "categoryId" character varying(255),
    stock integer DEFAULT 0,
    maincategoryid character varying(64),
    subcategoryid character varying(64),
    previousprice numeric,
    specialprice numeric,
    specialevent character varying(255),
    specialactive boolean DEFAULT false,
    specialstart timestamp without time zone,
    specialend timestamp without time zone
);


ALTER TABLE public.products OWNER TO iso_app;

--
-- Name: questions; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.questions (
    id character varying(255) NOT NULL,
    "clauseId" character varying CONSTRAINT questions_clauseid_not_null NOT NULL,
    text text NOT NULL,
    description text,
    type public.question_type NOT NULL,
    options jsonb,
    weight numeric(10,2) DEFAULT 1.0 NOT NULL,
    "order" integer NOT NULL,
    required boolean DEFAULT true NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT questions_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT questions_updatedat_not_null NOT NULL
);


ALTER TABLE public.questions OWNER TO iso_app;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    "sessionToken" character varying(255) CONSTRAINT sessions_sessiontoken_not_null NOT NULL,
    "userId" character varying(255) CONSTRAINT sessions_userid_not_null NOT NULL,
    expires timestamp without time zone NOT NULL
);


ALTER TABLE public.sessions OWNER TO iso_app;

--
-- Name: site_settings; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.site_settings (
    id character varying(255) NOT NULL,
    key character varying(255) NOT NULL,
    value text,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    category character varying(100),
    type character varying(50) DEFAULT 'string'::character varying,
    label character varying(255),
    description text,
    ispublic boolean DEFAULT false,
    requiresrestart boolean DEFAULT false,
    createdat timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    updatedat timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    currency character varying(8) DEFAULT 'USD'::character varying,
    currencysymbol character varying(8) DEFAULT '$'::character varying,
    inventoryenabled boolean DEFAULT true,
    lowstockthreshold integer DEFAULT 5
);


ALTER TABLE public.site_settings OWNER TO iso_app;

--
-- Name: standards; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.standards (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    version character varying(32) NOT NULL,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.standards OWNER TO postgres;

--
-- Name: standards_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.standards_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.standards_id_seq OWNER TO postgres;

--
-- Name: standards_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.standards_id_seq OWNED BY public.standards.id;


--
-- Name: terms_and_conditions; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.terms_and_conditions (
    id integer NOT NULL,
    title character varying(255) NOT NULL,
    content text NOT NULL,
    version character varying(32) NOT NULL,
    is_active boolean DEFAULT true,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE public.terms_and_conditions OWNER TO postgres;

--
-- Name: terms_and_conditions_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.terms_and_conditions_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.terms_and_conditions_id_seq OWNER TO postgres;

--
-- Name: terms_and_conditions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.terms_and_conditions_id_seq OWNED BY public.terms_and_conditions.id;


--
-- Name: user_answers; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_answers (
    id integer NOT NULL,
    assessment_id character varying(255),
    question_id character varying,
    answer text,
    evidence_url text,
    evidence_status character varying(32) DEFAULT 'pending'::character varying
);


ALTER TABLE public.user_answers OWNER TO postgres;

--
-- Name: user_answers_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_answers_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_answers_id_seq OWNER TO postgres;

--
-- Name: user_answers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_answers_id_seq OWNED BY public.user_answers.id;


--
-- Name: user_assessments; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_assessments (
    id character varying(255) NOT NULL,
    user_id uuid NOT NULL,
    standard_id integer,
    standard_version character varying(32) NOT NULL,
    started_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    completed_at timestamp without time zone,
    status character varying(32) DEFAULT 'draft'::character varying,
    score numeric,
    maturity_level character varying(32)
);


ALTER TABLE public.user_assessments OWNER TO postgres;

--
-- Name: user_assessments_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_assessments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_assessments_id_seq OWNER TO postgres;

--
-- Name: user_assessments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_assessments_id_seq OWNED BY public.user_assessments.id;


--
-- Name: user_terms_acceptances; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.user_terms_acceptances (
    id integer NOT NULL,
    user_id uuid NOT NULL,
    terms_id integer NOT NULL,
    accepted_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    ip_address character varying(64),
    user_agent text
);


ALTER TABLE public.user_terms_acceptances OWNER TO postgres;

--
-- Name: user_terms_acceptances_id_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.user_terms_acceptances_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_terms_acceptances_id_seq OWNER TO postgres;

--
-- Name: user_terms_acceptances_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.user_terms_acceptances_id_seq OWNED BY public.user_terms_acceptances.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.users (
    id character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    "emailVerified" timestamp without time zone,
    password character varying(255),
    name character varying(255),
    image character varying(500),
    role public.user_role DEFAULT 'USER'::public.user_role NOT NULL,
    "createdAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT users_createdat_not_null NOT NULL,
    "updatedAt" timestamp without time zone DEFAULT CURRENT_TIMESTAMP CONSTRAINT users_updatedat_not_null NOT NULL
);


ALTER TABLE public.users OWNER TO iso_app;

--
-- Name: verification_tokens; Type: TABLE; Schema: public; Owner: iso_app
--

CREATE TABLE public.verification_tokens (
    identifier character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    expires timestamp without time zone NOT NULL
);


ALTER TABLE public.verification_tokens OWNER TO iso_app;

--
-- Name: about_us id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.about_us ALTER COLUMN id SET DEFAULT nextval('public.about_us_id_seq'::regclass);


--
-- Name: iso_settings id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.iso_settings ALTER COLUMN id SET DEFAULT nextval('public.iso_settings_id_seq'::regclass);


--
-- Name: partners id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partners ALTER COLUMN id SET DEFAULT nextval('public.partners_id_seq'::regclass);


--
-- Name: product_category_recommendations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_category_recommendations ALTER COLUMN id SET DEFAULT nextval('public.product_category_recommendations_id_seq'::regclass);


--
-- Name: product_images id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images ALTER COLUMN id SET DEFAULT nextval('public.product_images_id_seq'::regclass);


--
-- Name: product_recommendations id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_recommendations ALTER COLUMN id SET DEFAULT nextval('public.product_recommendations_id_seq'::regclass);


--
-- Name: standards id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.standards ALTER COLUMN id SET DEFAULT nextval('public.standards_id_seq'::regclass);


--
-- Name: terms_and_conditions id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.terms_and_conditions ALTER COLUMN id SET DEFAULT nextval('public.terms_and_conditions_id_seq'::regclass);


--
-- Name: user_answers id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_answers ALTER COLUMN id SET DEFAULT nextval('public.user_answers_id_seq'::regclass);


--
-- Name: user_assessments id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_assessments ALTER COLUMN id SET DEFAULT nextval('public.user_assessments_id_seq'::regclass);


--
-- Name: user_terms_acceptances id; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_terms_acceptances ALTER COLUMN id SET DEFAULT nextval('public.user_terms_acceptances_id_seq'::regclass);


--
-- Data for Name: about_us; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.about_us (id, tagline, vision, mission, services, updated_at) FROM stdin;
1		lewis		[""]	2025-12-30 15:58:15.085908
\.


--
-- Data for Name: accounts; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.accounts (id, "userId", type, provider, "providerAccountId", refresh_token, access_token, expires_at, token_type, scope, id_token, session_state) FROM stdin;
\.


--
-- Data for Name: answers; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.answers (id, "assessmentId", "questionId", value, "textValue", score, "createdAt", "updatedAt") FROM stdin;
0075dca99d415aac9aa2d85f47960671	dd0b95d5ec546b9d327057f46d40c960	srskaxtiqcfjuv8by8vw88	3	\N	\N	2026-01-01 12:19:39.305	2026-01-01 12:25:14.824286
accebfa032a629d694e825d0649b14de	95eff9fe07da75fb710212a91f1d656a	3swcs2dgct92tqct12vk4k	1	\N	\N	2026-01-01 13:59:35.895	2026-01-01 14:01:59.686467
6e30270a3f91f47368a0622fcc858191	95eff9fe07da75fb710212a91f1d656a	mensmr1fohekeas60e4rz8	0	\N	\N	2026-01-01 13:59:37.907	2026-01-01 14:01:59.760251
d2e17143077e10c121730d21ce667b04	95eff9fe07da75fb710212a91f1d656a	95ppm4obxjapsbe0zanyc9	2	\N	\N	2026-01-01 13:59:39.253	2026-01-01 14:01:59.836008
3e028d49abeec950a98fe1b33eae9184	95eff9fe07da75fb710212a91f1d656a	alzubkpfnst0uwayiuniu4s	3	\N	\N	2026-01-01 13:59:40.638	2026-01-01 14:01:59.910053
a6feaecbe41c6135f0a08d34332f8507	95eff9fe07da75fb710212a91f1d656a	vja0lqg7y1oy48fk75v6gc	1	\N	\N	2026-01-01 13:59:48.777	2026-01-01 14:01:59.981389
55fda5242954f6fa211bd70239514795	95eff9fe07da75fb710212a91f1d656a	xma53nga6396ko6sfzxfma	1	\N	\N	2026-01-01 13:59:47.24	2026-01-01 14:02:00.044563
f36768325091b0b313dee423ea8e0d2f	95eff9fe07da75fb710212a91f1d656a	ys9lbdlcb99tr3m24g9o	3	\N	\N	2026-01-01 13:59:45.991	2026-01-01 14:02:00.102322
fd098be4344816b0d334663a544b9352	19bdbf99e6794bbc7c27f8f3f8f315f2	q4_1	YES	\N	\N	2025-12-29 13:01:56.825	2025-12-29 13:01:56.825
25c1f5595df1ebb0df7f2580a9d405ec	19bdbf99e6794bbc7c27f8f3f8f315f2	q4_2	NO	\N	\N	2025-12-29 13:01:58.215	2025-12-29 13:01:58.215
23764f31ec4c535ca422a1b9e29b48bd	19bdbf99e6794bbc7c27f8f3f8f315f2	q4_3	YES	\N	\N	2025-12-29 13:01:59.514	2025-12-29 13:01:59.514
ebeebd0b10435a0cfca694fe4853e701	19bdbf99e6794bbc7c27f8f3f8f315f2	q5_1	YES	\N	\N	2025-12-29 13:02:02.442	2025-12-29 13:02:02.442
e338992be4f327a1f415c1fb377e5d24	19bdbf99e6794bbc7c27f8f3f8f315f2	q5_2	NO	\N	\N	2025-12-29 13:02:03.384	2025-12-29 13:02:03.384
7bcfb798f1b81de0c57a9a732d67a2a4	19bdbf99e6794bbc7c27f8f3f8f315f2	q5_3	YES	\N	\N	2025-12-29 13:02:04.374	2025-12-29 13:02:04.374
e040949303335a353c51a8289873babd	19bdbf99e6794bbc7c27f8f3f8f315f2	q6_1	YES	\N	\N	2025-12-29 13:02:07.277	2025-12-29 13:02:07.277
69e33dc631a94405cb88f2b34dddc2ef	19bdbf99e6794bbc7c27f8f3f8f315f2	q6_2	NO	\N	\N	2025-12-29 13:02:08.35	2025-12-29 13:02:08.35
69e5a4f11a2d0ac078a7b307b130cc82	19bdbf99e6794bbc7c27f8f3f8f315f2	q6_3	YES	\N	\N	2025-12-29 13:02:09.431	2025-12-29 13:02:09.431
4d746dd3116b28adb3864322317a1689	19bdbf99e6794bbc7c27f8f3f8f315f2	q7_1	YES	\N	\N	2025-12-29 13:02:12.868	2025-12-29 13:02:12.868
6e858af6371b53cdbd2476dc41eaa440	19bdbf99e6794bbc7c27f8f3f8f315f2	q7_2	NO	\N	\N	2025-12-29 13:02:13.669	2025-12-29 13:02:13.669
963fd63067868595f5e8ca2fecce95a6	19bdbf99e6794bbc7c27f8f3f8f315f2	q7_3	YES	\N	\N	2025-12-29 13:02:15.18	2025-12-29 13:02:15.18
fa0f321815a1af98b2fc305ab0b97bff	19bdbf99e6794bbc7c27f8f3f8f315f2	q7_4	YES	\N	\N	2025-12-29 13:02:16.416	2025-12-29 13:02:16.416
f4c3944669a8956282bbbbc0cfd46038	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_1	YES	\N	\N	2025-12-29 13:02:20.295	2025-12-29 13:02:20.295
35a612c70d6ebe2cb108843f7aa8eb82	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_2	YES	\N	\N	2025-12-29 13:02:21.886	2025-12-29 13:02:21.886
c54649e5b8fa5108f72fb8f17a3b301a	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_3	YES	\N	\N	2025-12-29 13:02:24.426	2025-12-29 13:02:24.426
c4adf4f9a231eb46a4b56995ad200c09	825d444c90afd64fb97017744a90d376	q4_2	NO	\N	\N	2025-12-29 12:38:46.993	2025-12-29 14:32:00.925506
d8836fa35eb5b5ff7b34b4b186dd786e	825d444c90afd64fb97017744a90d376	q4_3	YES	\N	\N	2025-12-29 12:38:52.16	2025-12-29 14:32:01.01313
05dd3ada339fa6e9e74d75df2f500130	825d444c90afd64fb97017744a90d376	q5_1	YES	\N	\N	2025-12-29 12:39:24.898	2025-12-29 14:32:01.086765
f6f522bdf06d8d1d66c5018ce0d87661	825d444c90afd64fb97017744a90d376	q5_2	NO	\N	\N	2025-12-29 12:39:27.764	2025-12-29 14:32:01.165148
5cd964386d4cc25b4ef15162e9663cf7	825d444c90afd64fb97017744a90d376	q5_3	YES	\N	\N	2025-12-29 12:39:30.669	2025-12-29 14:32:01.22689
6e6853f625b5ccd7f501836769a99ab1	825d444c90afd64fb97017744a90d376	q6_1	YES	\N	\N	2025-12-29 12:39:37.438	2025-12-29 14:32:01.293933
62a78ad93a283af57ff7aec4a12037d0	825d444c90afd64fb97017744a90d376	q6_2	NO	\N	\N	2025-12-29 12:39:39.47	2025-12-29 14:32:01.37515
c4057522f166e950bde7fa8a086fcd6f	825d444c90afd64fb97017744a90d376	q6_3	YES	\N	\N	2025-12-29 12:39:43.446	2025-12-29 14:32:01.450014
6d1b958db7e967a07271b7bd5cbe13f9	825d444c90afd64fb97017744a90d376	q7_1	YES	\N	\N	2025-12-29 12:39:53.078	2025-12-29 14:32:01.513441
bfcab72d365e1f022b72bee8b5ad0084	825d444c90afd64fb97017744a90d376	q7_2	NO	\N	\N	2025-12-29 12:39:54.25	2025-12-29 14:32:01.583462
5bf885056772802820b52563cae93925	825d444c90afd64fb97017744a90d376	q7_3	YES	\N	\N	2025-12-29 12:39:54.939	2025-12-29 14:32:01.663121
b29ce59f7d1008d89e98ee90b71fe0c7	825d444c90afd64fb97017744a90d376	q7_4	NO	\N	\N	2025-12-29 12:39:55.709	2025-12-29 14:32:01.745878
ce7cf35459a3c551b6d21f059ef3a3b5	825d444c90afd64fb97017744a90d376	q8_2	YES	\N	\N	2025-12-29 12:39:59.969	2025-12-29 14:32:01.820747
42c8170e6c069235d09df99502338aaa	825d444c90afd64fb97017744a90d376	q8_3	NO	\N	\N	2025-12-29 12:40:00.766	2025-12-29 14:32:01.924231
a95915753088109552f8d607e1a61e52	825d444c90afd64fb97017744a90d376	q8_4	YES	\N	\N	2025-12-29 12:40:01.384	2025-12-29 14:32:02.003622
632f5131906c5c20f4feed48b90659c8	825d444c90afd64fb97017744a90d376	q8_5	NO	\N	\N	2025-12-29 12:40:01.976	2025-12-29 14:32:02.065684
c8925802ea2985cc15511a82881b61e7	825d444c90afd64fb97017744a90d376	q8_6	YES	\N	\N	2025-12-29 12:40:03.575	2025-12-29 14:32:02.162489
8bf9ec855b52705846f21d9255c60eb8	825d444c90afd64fb97017744a90d376	q8_7	NO	\N	\N	2025-12-29 12:40:04.156	2025-12-29 14:32:02.242129
c3b053abfefb3e758a50380be75cc028	825d444c90afd64fb97017744a90d376	q9_1	YES	\N	\N	2025-12-29 12:40:07.271	2025-12-29 14:32:02.312858
961eec274b36ec99c7fd826c63eca562	825d444c90afd64fb97017744a90d376	q9_2	NO	\N	\N	2025-12-29 12:40:08.077	2025-12-29 14:32:02.379708
36c1e99b55ecb71e7904a2bfde18c49c	825d444c90afd64fb97017744a90d376	q9_3	YES	\N	\N	2025-12-29 12:40:08.646	2025-12-29 14:32:02.456941
6c05adf44407d4d19216170f8fa373f7	825d444c90afd64fb97017744a90d376	q10_1	YES	\N	\N	2025-12-29 12:40:22.328	2025-12-29 14:32:02.51971
8c8cdb3518523227f9cbfcc9176b91c1	825d444c90afd64fb97017744a90d376	q10_2	NO	\N	\N	2025-12-29 12:40:23.113	2025-12-29 14:32:02.588955
27dbc68604e96f9e7668139288554871	95eff9fe07da75fb710212a91f1d656a	3yo6a79rhbegcp8r8uq7yi	1	\N	\N	2026-01-01 13:59:45.31	2026-01-01 14:02:00.173612
b9d735df8a6a52a913b82569698b8386	95eff9fe07da75fb710212a91f1d656a	22wqksbbnvqis8jr22afsjl	2	\N	\N	2026-01-01 13:59:43.293	2026-01-01 14:02:00.263759
fd8710119a5cf8c616b5fbc447750707	95eff9fe07da75fb710212a91f1d656a	wc0bw5redk5t19bm4ng	0	\N	\N	2026-01-01 13:59:55.074	2026-01-01 14:02:00.321448
efd3f6dcf7907fb670964c9b4b3a94e6	95eff9fe07da75fb710212a91f1d656a	9whvxpv75ofvxxyxic6c7	1	\N	\N	2026-01-01 13:59:56.524	2026-01-01 14:02:00.379846
ca96b02e04036700986d3ff19a69d222	95eff9fe07da75fb710212a91f1d656a	pc2aowfti62zxgq6piiut	1	\N	\N	2026-01-01 13:59:57.724	2026-01-01 14:02:00.445395
7c527b5af9f023d08ac5268a01da21a0	95eff9fe07da75fb710212a91f1d656a	fk1xlwzokimp5rvcpg2dop	3	\N	\N	2026-01-01 13:59:59.321	2026-01-01 14:02:00.52577
078fa6b8e7644aa6608002c504530c75	95eff9fe07da75fb710212a91f1d656a	89nd28dz5ybw57r7wm1lx	1	\N	\N	2026-01-01 14:00:00.367	2026-01-01 14:02:00.590339
a20a9b10dcce497e8fca4e5ae98042aa	95eff9fe07da75fb710212a91f1d656a	hoc52w2etydetdlfvpz8lw	1	\N	\N	2026-01-01 14:00:13.383	2026-01-01 14:02:00.66314
3f2bcd126d5d1292535aa6abdaafbac3	95eff9fe07da75fb710212a91f1d656a	0nuho526fvmghe1e73sbt5	2	\N	\N	2026-01-01 14:00:05.242	2026-01-01 14:02:00.722532
83fb50fbe599716a0a97db9e2be106ae	95eff9fe07da75fb710212a91f1d656a	kgn2jku42jryy23wlb82s	1	\N	\N	2026-01-01 14:00:06.144	2026-01-01 14:02:00.784366
9e8096d10e9e9fff7ec3c18d4ed46957	95eff9fe07da75fb710212a91f1d656a	nge0uunzdmoejzyxf7j7e	3	\N	\N	2026-01-01 14:00:07.337	2026-01-01 14:02:00.847361
17d7a530e785e7723e2b6d26fe020fa2	95eff9fe07da75fb710212a91f1d656a	6f3b0uyirnsovx7u0myll	3	\N	\N	2026-01-01 14:00:09.085	2026-01-01 14:02:00.910542
bbe0911ac6f44b2face6c40103044dbc	95eff9fe07da75fb710212a91f1d656a	z8cbnifd2vsz2y0f71rfgg	0	\N	\N	2026-01-01 14:00:22.129	2026-01-01 14:02:00.97628
a506ca38900fe4fd79bdd2f9808f85f6	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_5	YES	\N	\N	2025-12-29 13:02:28.152	2025-12-29 13:02:28.152
94e031faec814e3ac8807d94b2f9092f	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_6	YES	\N	\N	2025-12-29 13:02:29.131	2025-12-29 13:02:29.131
4ac805c63b35d2673cd1f050d52375a5	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_7	YES	\N	\N	2025-12-29 13:02:29.985	2025-12-29 13:02:29.985
28e5beffe48d67765e486c6667fa89a4	19bdbf99e6794bbc7c27f8f3f8f315f2	q8_4	NO	\N	\N	2025-12-29 13:02:34.145	2025-12-29 13:02:34.145
94744e67f006ca683dbbefd2e85b736d	19bdbf99e6794bbc7c27f8f3f8f315f2	q9_1	YES	\N	\N	2025-12-29 13:02:37.77	2025-12-29 13:02:37.77
8227dd398d7d24b75a462d08ea1a4c09	19bdbf99e6794bbc7c27f8f3f8f315f2	q9_2	YES	\N	\N	2025-12-29 13:02:38.566	2025-12-29 13:02:38.566
0be37a830e567f4b47d3b7b9a41a3d85	19bdbf99e6794bbc7c27f8f3f8f315f2	q9_3	YES	\N	\N	2025-12-29 13:02:40.733	2025-12-29 13:02:40.733
dca664a99503c16c88b06ba7c83a2898	19bdbf99e6794bbc7c27f8f3f8f315f2	q10_1	YES	\N	\N	2025-12-29 13:02:45.054	2025-12-29 13:02:45.054
897e0c4b374cf2754aa3b63b244b0834	19bdbf99e6794bbc7c27f8f3f8f315f2	q10_2	YES	\N	\N	2025-12-29 13:02:45.973	2025-12-29 13:02:45.973
b0b1ee54b10925f92cc7e45f66361ede	8e4f8bc566a44dfe27a95b7bf70c994d	q4_1	YES	\N	\N	2025-12-29 13:13:42.155	2025-12-29 13:13:42.155
078143ebfa81aac02132add759bfd03f	8e4f8bc566a44dfe27a95b7bf70c994d	q4_2	YES	\N	\N	2025-12-29 13:13:43.035	2025-12-29 13:13:43.035
d76e0e4e71534ccbec0d25ec2e9eea8c	8e4f8bc566a44dfe27a95b7bf70c994d	q4_3	YES	\N	\N	2025-12-29 13:13:44.133	2025-12-29 13:13:44.133
4a205d80d471abc114cb123e7123ef88	8e4f8bc566a44dfe27a95b7bf70c994d	q5_1	YES	\N	\N	2025-12-29 13:15:18.289	2025-12-29 13:15:18.289
5a93964dc53fe819ee5d73c91593bd23	8e4f8bc566a44dfe27a95b7bf70c994d	q5_2	YES	\N	\N	2025-12-29 13:15:19.459	2025-12-29 13:15:19.459
445e8c89f0d3d37b5ea5fb9e3e62e873	8e4f8bc566a44dfe27a95b7bf70c994d	q5_3	NO	\N	\N	2025-12-29 13:15:20.484	2025-12-29 13:15:20.484
eee3656b5e02c5db8e1739d9c37bd833	8e4f8bc566a44dfe27a95b7bf70c994d	q6_1	YES	\N	\N	2025-12-29 13:15:24.32	2025-12-29 13:15:24.32
83c0125bd4809e01f723c1c804f15a51	8e4f8bc566a44dfe27a95b7bf70c994d	q6_2	YES	\N	\N	2025-12-29 13:16:14.827	2025-12-29 13:16:14.827
333ae8d29335864ceec9f16452ae1719	8e4f8bc566a44dfe27a95b7bf70c994d	q6_3	YES	\N	\N	2025-12-29 13:16:16.884	2025-12-29 13:16:16.884
5ff1cb3ce171bcfb714a02eda65c9ecc	8e4f8bc566a44dfe27a95b7bf70c994d	q7_1	YES	\N	\N	2025-12-29 13:16:19.753	2025-12-29 13:16:19.753
0318f900b574d6bc88a0d5033647600f	8e4f8bc566a44dfe27a95b7bf70c994d	q7_2	YES	\N	\N	2025-12-29 13:16:20.706	2025-12-29 13:16:20.706
9a3d57e0d2161d9b9b94e3a6bf96f9af	8e4f8bc566a44dfe27a95b7bf70c994d	q7_3	YES	\N	\N	2025-12-29 13:16:21.622	2025-12-29 13:16:21.622
57dceb5003cc094d3ee597998930d680	8e4f8bc566a44dfe27a95b7bf70c994d	q7_4	YES	\N	\N	2025-12-29 13:16:22.459	2025-12-29 13:16:22.459
4debc407d7e4225754da4f01a2631656	8e4f8bc566a44dfe27a95b7bf70c994d	q8_2	YES	\N	\N	2025-12-29 13:16:38.455	2025-12-29 13:16:38.455
f79df97c2a8f6e6d9e352f3b0ee26fcd	8e4f8bc566a44dfe27a95b7bf70c994d	q8_1	YES	\N	\N	2025-12-29 13:16:42.342	2025-12-29 13:16:42.342
3edaca2cbafe8e5acab9fb423f04d1bf	8e4f8bc566a44dfe27a95b7bf70c994d	q8_6	YES	\N	\N	2025-12-29 13:16:44.386	2025-12-29 13:16:44.386
ef5cf89dad95692fa7b7c3bd344b250f	8e4f8bc566a44dfe27a95b7bf70c994d	q8_7	YES	\N	\N	2025-12-29 13:16:45.367	2025-12-29 13:16:45.367
35aa3e6020153dcf5af7e615acc3a472	8e4f8bc566a44dfe27a95b7bf70c994d	q8_5	YES	\N	\N	2025-12-29 13:16:46.998	2025-12-29 13:16:46.998
ce12461b6da46bcf52adfadad11a08a1	8e4f8bc566a44dfe27a95b7bf70c994d	q8_4	YES	\N	\N	2025-12-29 13:16:47.979	2025-12-29 13:16:47.979
60b8cdbb13917aea79e231db4bccfad0	8e4f8bc566a44dfe27a95b7bf70c994d	q9_1	YES	\N	\N	2025-12-29 13:17:22.959	2025-12-29 13:17:22.959
db42e11fc1454847f15f294e78c2c058	8e4f8bc566a44dfe27a95b7bf70c994d	q9_2	YES	\N	\N	2025-12-29 13:17:23.664	2025-12-29 13:17:23.664
5b66096a6756b121bc6f740cc23a2071	8e4f8bc566a44dfe27a95b7bf70c994d	q9_3	YES	\N	\N	2025-12-29 13:17:24.384	2025-12-29 13:17:24.384
fa96ce20d0e8294d9a03e1c535190f07	8e4f8bc566a44dfe27a95b7bf70c994d	q10_1	YES	\N	\N	2025-12-29 13:17:27.685	2025-12-29 13:17:27.685
c3638c4657b92a348508c6c1187b110e	8e4f8bc566a44dfe27a95b7bf70c994d	q10_2	YES	\N	\N	2025-12-29 13:17:28.764	2025-12-29 13:17:28.764
f75eef61fde794b560985d0d0fe7fd35	825d444c90afd64fb97017744a90d376	q4_1	YES	\N	\N	2025-12-29 12:38:43.729	2025-12-29 14:32:00.823936
66dae8d829705ef7a5ce22fa43cd0715	f4aa5b1635a8c36709d45b9d5c386f33	q4_1	YES	\N	\N	2025-12-29 18:43:06.076	2025-12-29 18:43:53.115199
f08f593d94d623b91bfb92a516ebb257	f4aa5b1635a8c36709d45b9d5c386f33	q4_2	NO	\N	\N	2025-12-29 18:43:07.209	2025-12-29 18:43:53.216739
3547531d36cf1d47637d8573f5e5bf66	f4aa5b1635a8c36709d45b9d5c386f33	q4_3	YES	\N	\N	2025-12-29 18:43:08.046	2025-12-29 18:43:53.298318
ebd4de36e9c06fe757814e31f1f8bbf0	f4aa5b1635a8c36709d45b9d5c386f33	q5_1	YES	\N	\N	2025-12-29 18:43:11.501	2025-12-29 18:43:53.388919
3499bb5abf9adbb0919e110d82ee34b3	f4aa5b1635a8c36709d45b9d5c386f33	q5_2	NO	\N	\N	2025-12-29 18:43:13.581	2025-12-29 18:43:53.475555
4c4a4a25f789d9f0e166ad4259aa51d5	f4aa5b1635a8c36709d45b9d5c386f33	q5_3	YES	\N	\N	2025-12-29 18:43:15.226	2025-12-29 18:43:53.563599
0c8f21bc090af4bd27e93b6808653509	f4aa5b1635a8c36709d45b9d5c386f33	q6_1	YES	\N	\N	2025-12-29 18:43:18.427	2025-12-29 18:43:53.640222
93c4b75f2bd2265e2eb289656abef154	f4aa5b1635a8c36709d45b9d5c386f33	q6_2	NO	\N	\N	2025-12-29 18:43:21.034	2025-12-29 18:43:53.721724
e1364f809ee62cd3f90b3af28e176cbd	f4aa5b1635a8c36709d45b9d5c386f33	q6_3	YES	\N	\N	2025-12-29 18:43:21.939	2025-12-29 18:43:53.811296
a5290a09f677ebd4f213e71fe9330428	f4aa5b1635a8c36709d45b9d5c386f33	q7_1	YES	\N	\N	2025-12-29 18:43:24.819	2025-12-29 18:43:53.889255
d1d00efc8877ffba0fdfc731c2995561	f4aa5b1635a8c36709d45b9d5c386f33	q7_2	NO	\N	\N	2025-12-29 18:43:25.619	2025-12-29 18:43:53.973899
ba72f50c03f7df080b591dceb1dcd365	f4aa5b1635a8c36709d45b9d5c386f33	q7_3	YES	\N	\N	2025-12-29 18:43:26.543	2025-12-29 18:43:54.054495
057c7cf4ac8e0f027c20320f4e326390	f4aa5b1635a8c36709d45b9d5c386f33	q7_4	YES	\N	\N	2025-12-29 18:43:27.31	2025-12-29 18:43:54.142137
79f3b47a150eaec90e0c2d2a0c1001c4	f4aa5b1635a8c36709d45b9d5c386f33	q8_1	YES	\N	\N	2025-12-29 18:43:29.776	2025-12-29 18:43:54.299095
f8e375c09781071442e3a32bed9854cf	f4aa5b1635a8c36709d45b9d5c386f33	q8_2	YES	\N	\N	2025-12-29 18:43:33.665	2025-12-29 18:43:54.471278
6f69305fe580e202bbc7425526222cfd	f4aa5b1635a8c36709d45b9d5c386f33	q8_3	YES	\N	\N	2025-12-29 18:43:34.547	2025-12-29 18:43:54.573917
5d94f8ed0c44e761e0e9ed9a24575fd3	f4aa5b1635a8c36709d45b9d5c386f33	q8_4	YES	\N	\N	2025-12-29 18:43:35.418	2025-12-29 18:43:54.654364
46dcbdcc86afe9aef377377e0f62632d	f4aa5b1635a8c36709d45b9d5c386f33	q8_5	YES	\N	\N	2025-12-29 18:43:37.984	2025-12-29 18:43:54.740516
889527bcce23ffcb030993c51bcf2180	f4aa5b1635a8c36709d45b9d5c386f33	q8_6	YES	\N	\N	2025-12-29 18:43:39.018	2025-12-29 18:43:54.830835
fddc3e0db6542e2f8c6c68b1adfbd477	f4aa5b1635a8c36709d45b9d5c386f33	q8_7	YES	\N	\N	2025-12-29 18:43:40.765	2025-12-29 18:43:54.91235
c82b2066788e5b5ca3416b545dc9851b	f4aa5b1635a8c36709d45b9d5c386f33	q9_1	YES	\N	\N	2025-12-29 18:43:45.779	2025-12-29 18:43:54.991842
6deeff829dda7cd4b6d293a9946ad0cc	f4aa5b1635a8c36709d45b9d5c386f33	q9_2	NO	\N	\N	2025-12-29 18:43:47.51	2025-12-29 18:43:55.080969
880917a3910ab500261bf7b511416b82	f4aa5b1635a8c36709d45b9d5c386f33	q9_3	NO	\N	\N	2025-12-29 18:43:48.261	2025-12-29 18:43:55.162495
cfeb1e401b27d577eca4978df73ac490	f4aa5b1635a8c36709d45b9d5c386f33	q10_1	NO	\N	\N	2025-12-29 18:43:50.809	2025-12-29 18:43:55.240073
2d04cfd868563c0d038160b462449861	f4aa5b1635a8c36709d45b9d5c386f33	q10_2	NO	\N	\N	2025-12-29 18:43:51.688	2025-12-29 18:43:55.32758
5295a90d7642d58379cb2dd078b1fd3c	07eebf55e5695e59b9050b2e2cbda760	q4_1	YES	\N	\N	2025-12-30 04:18:01.568	2025-12-30 04:18:01.568
466feb24492288889c8d7cb1c7d78144	07eebf55e5695e59b9050b2e2cbda760	q4_2	YES	\N	\N	2025-12-30 04:18:21.725	2025-12-30 04:18:21.725
6aae8f2f7b85c97d6064b674df74c081	07eebf55e5695e59b9050b2e2cbda760	q4_3	YES	\N	\N	2025-12-30 04:18:27.742	2025-12-30 04:18:27.742
1013e8045e2f3f13fdd829511130b37c	07eebf55e5695e59b9050b2e2cbda760	q5_1	YES	\N	\N	2025-12-30 04:18:44.509	2025-12-30 04:18:44.509
f7791301782b632905f1bbde08f78a49	07eebf55e5695e59b9050b2e2cbda760	q5_2	YES	\N	\N	2025-12-30 04:18:49.647	2025-12-30 04:18:49.647
0fba39923fef7bd64df4a8896d0da4f6	07eebf55e5695e59b9050b2e2cbda760	q5_3	YES	\N	\N	2025-12-30 04:18:52.885	2025-12-30 04:18:52.885
de1d2f4a5488266c079b978b35969ba0	07eebf55e5695e59b9050b2e2cbda760	q6_1	YES	\N	\N	2025-12-30 04:19:04.134	2025-12-30 04:19:04.134
172989a15895099f976c5e969fdd4b2a	07eebf55e5695e59b9050b2e2cbda760	q6_2	YES	\N	\N	2025-12-30 04:19:08.58	2025-12-30 04:19:08.58
23462c2f54bef0af8e8f44c47f96e9ed	07eebf55e5695e59b9050b2e2cbda760	q6_3	NO	\N	\N	2025-12-30 04:19:12.474	2025-12-30 04:19:12.474
bddb100f6ec3e3148fb9b9075233040c	51942337b668c47200b624b162cbd3e6	q9_2	YES	\N	\N	2025-12-30 04:22:04.673	2025-12-30 04:22:38.203799
168d63f526072ce92fb9856c5c7256e9	51942337b668c47200b624b162cbd3e6	q9_3	NO	\N	\N	2025-12-30 04:22:08.342	2025-12-30 04:22:38.793352
0d2d644ce689a0318340fbb3adc24304	51942337b668c47200b624b162cbd3e6	q4_1	YES	\N	\N	2025-12-30 04:19:59.739	2025-12-30 04:22:24.324218
d5ed32c357c15409bf13f4f42caaa31a	51942337b668c47200b624b162cbd3e6	q4_2	YES	\N	\N	2025-12-30 04:20:01.153	2025-12-30 04:22:24.91702
ea67bacb4d4140e489cf33821524b8e0	51942337b668c47200b624b162cbd3e6	q4_3	YES	\N	\N	2025-12-30 04:20:02.526	2025-12-30 04:22:25.665396
d005f3d731d485a2447df1b638ba5ebf	51942337b668c47200b624b162cbd3e6	q5_1	YES	\N	\N	2025-12-30 04:20:07.403	2025-12-30 04:22:26.249838
3aa35bab608361aa16475d2c6142cc43	51942337b668c47200b624b162cbd3e6	q5_2	YES	\N	\N	2025-12-30 04:20:08.31	2025-12-30 04:22:26.907436
bdc6a06a48322830934d20b90c5cb8ff	51942337b668c47200b624b162cbd3e6	q5_3	YES	\N	\N	2025-12-30 04:20:09.683	2025-12-30 04:22:27.572389
c35fcc79ac5d9be05b556da89f363f18	51942337b668c47200b624b162cbd3e6	q6_1	YES	\N	\N	2025-12-30 04:20:12.622	2025-12-30 04:22:28.148219
79fd939b64f7538ae39d6a1e6e2997b7	51942337b668c47200b624b162cbd3e6	q6_2	YES	\N	\N	2025-12-30 04:20:13.978	2025-12-30 04:22:28.906143
8d9885082041d61dfed378b212f1dfb1	51942337b668c47200b624b162cbd3e6	q6_3	NO	\N	\N	2025-12-30 04:20:15.263	2025-12-30 04:22:29.556928
6f735f5144f678c364158c999540cc5b	51942337b668c47200b624b162cbd3e6	q7_1	NO	\N	\N	2025-12-30 04:20:18.977	2025-12-30 04:22:30.341696
f78f13481876cb94873ea104e97e732f	51942337b668c47200b624b162cbd3e6	q7_2	NO	\N	\N	2025-12-30 04:20:18.251	2025-12-30 04:22:30.909271
c9bbe145c4df212cf17638704bd24ef0	51942337b668c47200b624b162cbd3e6	q7_3	YES	\N	\N	2025-12-30 04:20:20.761	2025-12-30 04:22:31.675665
f5a4bf0b0dcbf2a69d9e9495972a72f4	51942337b668c47200b624b162cbd3e6	q7_4	YES	\N	\N	2025-12-30 04:20:23.178	2025-12-30 04:22:32.454916
2d23ee548801b9e64fab6392cfa7d2ad	51942337b668c47200b624b162cbd3e6	q8_1	NO	\N	\N	2025-12-30 04:21:53.57	2025-12-30 04:22:33.144571
2d76dbae48d73a956cfb5e3f7c4c94f8	51942337b668c47200b624b162cbd3e6	q8_2	YES	\N	\N	2025-12-30 04:20:34.002	2025-12-30 04:22:33.747772
d2b2eeae971e859c482886fb91df9555	51942337b668c47200b624b162cbd3e6	q8_3	NO	\N	\N	2025-12-30 04:20:36.537	2025-12-30 04:22:34.354335
ef84e22236cf0cecef06ac7049ad5129	51942337b668c47200b624b162cbd3e6	q8_4	NO	\N	\N	2025-12-30 04:20:39.563	2025-12-30 04:22:35.088308
f289777ef8b1bfc3cea6a679ec964c9b	51942337b668c47200b624b162cbd3e6	q8_5	NO	\N	\N	2025-12-30 04:20:44.155	2025-12-30 04:22:35.671778
ff5ddfbd94354a839f5f73fb11483525	51942337b668c47200b624b162cbd3e6	q8_6	YES	\N	\N	2025-12-30 04:20:46.14	2025-12-30 04:22:36.336899
8c0d6346dafc64d6a4d9e983dc3c9849	51942337b668c47200b624b162cbd3e6	q8_7	YES	\N	\N	2025-12-30 04:20:51.979	2025-12-30 04:22:36.893973
5219c5a46e4f8d2815a82c767468b79c	51942337b668c47200b624b162cbd3e6	q9_1	NO	\N	\N	2025-12-30 04:22:03.259	2025-12-30 04:22:37.555072
b96827b351c146316b129e980e3464e4	51942337b668c47200b624b162cbd3e6	q10_1	NO	\N	\N	2025-12-30 04:22:17.231	2025-12-30 04:22:39.457736
1b428873129fa9b3543005b690622b6b	51942337b668c47200b624b162cbd3e6	q10_2	NO	\N	\N	2025-12-30 04:22:22.068	2025-12-30 04:22:40.205972
239bd0f00d8a38aba26a98639078727d	dd0b95d5ec546b9d327057f46d40c960	3swcs2dgct92tqct12vk4k	2	\N	\N	2026-01-01 12:25:20.337	2026-01-01 12:25:20.337
82f64250a84ff3df22a95722bd85e9fd	dd0b95d5ec546b9d327057f46d40c960	95ppm4obxjapsbe0zanyc9	2	\N	\N	2026-01-01 12:25:22.037	2026-01-01 12:25:27.115882
0c4f5e6a90bb143551154d5d17f08a04	dd0b95d5ec546b9d327057f46d40c960	mensmr1fohekeas60e4rz8	1	\N	\N	2026-01-01 12:25:30.689	2026-01-01 12:25:30.689
e88c3ff66fd9e610baa037eee20b480b	dd0b95d5ec546b9d327057f46d40c960	alzubkpfnst0uwayiuniu4s	3	\N	\N	2026-01-01 12:25:32.313	2026-01-01 12:25:34.236369
9fc1663f59bb5a0224f3354507d4049a	dd0b95d5ec546b9d327057f46d40c960	vja0lqg7y1oy48fk75v6gc	0	\N	\N	2026-01-01 12:25:38.337	2026-01-01 12:25:40.109217
5e1f8ae0c5fb51ca605de68db3669191	dd0b95d5ec546b9d327057f46d40c960	ys9lbdlcb99tr3m24g9o	1	\N	\N	2026-01-01 12:25:41.73	2026-01-01 12:25:41.73
e3890f9c5cb33f4de768aada5e03d1e0	dd0b95d5ec546b9d327057f46d40c960	xma53nga6396ko6sfzxfma	3	\N	\N	2026-01-01 12:25:43.956	2026-01-01 12:25:43.956
9dd395c590cb7d8af3575242186ae2f8	dd0b95d5ec546b9d327057f46d40c960	22wqksbbnvqis8jr22afsjl	0	\N	\N	2026-01-01 12:25:45.847	2026-01-01 12:25:45.847
de1f18b2c3b7882eb19c8601a2a07eae	dd0b95d5ec546b9d327057f46d40c960	3yo6a79rhbegcp8r8uq7yi	1	\N	\N	2026-01-01 12:25:47.14	2026-01-01 12:25:47.14
c50214ad98037671d09f78aa29509590	95eff9fe07da75fb710212a91f1d656a	397brzl7kmpt93v169girj	1	\N	\N	2026-01-01 14:00:26.009	2026-01-01 14:02:01.161554
fa07710c902f656c2114ea53a6f91f96	95eff9fe07da75fb710212a91f1d656a	23a8sx7d8acfwag9qf0or5	1	\N	\N	2026-01-01 14:00:27.417	2026-01-01 14:02:01.220527
c3eadd0c33c196d3dc4441c7bbbb464d	95eff9fe07da75fb710212a91f1d656a	dzysvuhcwkg2aq2blmc7re	1	\N	\N	2026-01-01 14:00:28.868	2026-01-01 14:02:01.291247
dc108a2bb8c118238ac34acf4b8b4b94	95eff9fe07da75fb710212a91f1d656a	hl8i8bf4gnwxorb84uelgb	1	\N	\N	2026-01-01 14:00:32.137	2026-01-01 14:02:01.347759
58536cc41dc5b395fb6581222b30382a	95eff9fe07da75fb710212a91f1d656a	rn1ojzoihhg3clx7ohk58	1	\N	\N	2026-01-01 14:00:32.925	2026-01-01 14:02:01.410212
9195bd865fdfa9209021ff08b8a4ad7c	95eff9fe07da75fb710212a91f1d656a	rxjsj0w3tekzviv471auu	3	\N	\N	2026-01-01 14:00:34.915	2026-01-01 14:02:01.469643
e136e55c40a3990e48f734eee0d340b5	95eff9fe07da75fb710212a91f1d656a	pg42ma61a6muggj8o7r7kc	3	\N	\N	2026-01-01 14:00:36.01	2026-01-01 14:02:01.537
8ef98d686bca4562cac39c8d32c378d1	95eff9fe07da75fb710212a91f1d656a	2ku501rpbhlyj9irir8eqq	3	\N	\N	2026-01-01 14:00:37.693	2026-01-01 14:02:01.601896
2fd78de1c90ccfcb100059fb3374d871	95eff9fe07da75fb710212a91f1d656a	vsqaw18d4wm315955pwhlq	1	\N	\N	2026-01-01 14:00:42.336	2026-01-01 14:02:01.680428
ad861fde61a6dd0ddfbb2a5626778dc2	95eff9fe07da75fb710212a91f1d656a	q1949rj0nwu4z5vm5qpbc	1	\N	\N	2026-01-01 14:00:43.834	2026-01-01 14:02:01.73672
7f7b8d438f007af6359976eb620a829b	95eff9fe07da75fb710212a91f1d656a	rev6g6t5bswq902tocedl	1	\N	\N	2026-01-01 14:00:45.71	2026-01-01 14:02:01.802257
82d90daffef6c2551ebaa2945d3eac7b	95eff9fe07da75fb710212a91f1d656a	c6h286ln5xg1v4m9hgphpp	3	\N	\N	2026-01-01 14:00:47.496	2026-01-01 14:02:01.860289
733a61bf0434584ed754eb6498816f96	95eff9fe07da75fb710212a91f1d656a	pg2g121k0y9uzb32gprbu	3	\N	\N	2026-01-01 14:00:49.141	2026-01-01 14:02:01.923411
ee9a59a859cd291200e0dc46a89caa39	95eff9fe07da75fb710212a91f1d656a	iwm6ytxvm7krxkb9m7gfqs	1	\N	\N	2026-01-01 14:00:53.592	2026-01-01 14:02:01.996545
bb03bf57e01896391d1af87ca15f9d26	95eff9fe07da75fb710212a91f1d656a	jwi9p6w2ism681gkf50prt	1	\N	\N	2026-01-01 14:00:54.822	2026-01-01 14:02:02.065709
bf433c81b294ac0f2dcaf0aed4d9d17b	95eff9fe07da75fb710212a91f1d656a	t5dedt02uzmxgc8m9jm3	1	\N	\N	2026-01-01 14:00:56.798	2026-01-01 14:02:02.123891
639e5fe60ab61eae259ce5f91ee51b5a	95eff9fe07da75fb710212a91f1d656a	1o3sje2xo6al33x9bkobc	1	\N	\N	2026-01-01 14:01:02.669	2026-01-01 14:02:02.185474
fa8980c62ebb1ae0e41581893620e5d0	95eff9fe07da75fb710212a91f1d656a	4wqj377hljv0h9grw0wobk	1	\N	\N	2026-01-01 14:01:04.929	2026-01-01 14:02:02.241681
0e6049205b44e0fd43f81159feb09f28	95eff9fe07da75fb710212a91f1d656a	v1w594veett0esgiw0bq	1	\N	\N	2026-01-01 14:01:06.259	2026-01-01 14:02:02.310004
f335d2cf87db8f8f2d4a854337bca14a	95eff9fe07da75fb710212a91f1d656a	as6m3c746nef0oud61gu8	1	\N	\N	2026-01-01 14:01:09.234	2026-01-01 14:02:02.376118
026b638b6141e90527897420389e5416	95eff9fe07da75fb710212a91f1d656a	hm7jwsduuwpigzw5oogp	1	\N	\N	2026-01-01 14:01:10.528	2026-01-01 14:02:02.444334
947d983be22975a530a8eef0a0e5b654	95eff9fe07da75fb710212a91f1d656a	srskaxtiqcfjuv8by8vw88	0	\N	\N	2026-01-01 13:59:33.917	2026-01-01 14:01:59.603662
d5f131cc1566ea839eabbd28058137bc	95eff9fe07da75fb710212a91f1d656a	z00i700mhvnd4ofn88zj	1	\N	\N	2026-01-01 14:00:23.74	2026-01-01 14:02:01.096847
ac0cb697a5f8733e9b1f77b63551e51e	95eff9fe07da75fb710212a91f1d656a	l1bk2jutz7cpnwutrpl5qp	1	\N	\N	2026-01-01 14:01:11.723	2026-01-01 14:02:02.532622
175549cfe0c6df4240c5da3b16ebffe8	95eff9fe07da75fb710212a91f1d656a	8ehwl6kjfzuvjnvbdqbcca	3	\N	\N	2026-01-01 14:01:24.024	2026-01-01 14:02:02.588341
4770c119a14a933119bb7d04d1210b0c	95eff9fe07da75fb710212a91f1d656a	bh108qb0yh5m4a4s5ikn4	3	\N	\N	2026-01-01 14:01:22.993	2026-01-01 14:02:02.65782
b3d0b18f7335547b1d4ce49621069ac0	95eff9fe07da75fb710212a91f1d656a	sekqj60xrps9n1wb52wnf	1	\N	\N	2026-01-01 14:01:15.244	2026-01-01 14:02:02.721412
26d0afce4337083d75b53f129fef8303	95eff9fe07da75fb710212a91f1d656a	i7qnxaod0qqun82i1e5k	1	\N	\N	2026-01-01 14:01:18.329	2026-01-01 14:02:02.788699
6db62a45a52550f3c3f4b1b13bf986b4	95eff9fe07da75fb710212a91f1d656a	rr3z81ut2up55z4pxrax9o	1	\N	\N	2026-01-01 14:01:35.329	2026-01-01 14:02:02.85222
ef5bb0c767dd4df3dccbc51e04e304ae	95eff9fe07da75fb710212a91f1d656a	v7qo1tt4nk01siewkr9jtc	1	\N	\N	2026-01-01 14:01:33.979	2026-01-01 14:02:02.9271
1b0b6d10efb82a18aaf68afe48238eec	95eff9fe07da75fb710212a91f1d656a	w7ipjp447tmrbiya1okcv	1	\N	\N	2026-01-01 14:01:32.366	2026-01-01 14:02:02.992909
484078cdd70d3996f5a27d408b7fca48	95eff9fe07da75fb710212a91f1d656a	3r5jen1qwsmqx68y6fji1	1	\N	\N	2026-01-01 14:01:41.873	2026-01-01 14:02:03.055667
26ccdb3f248d7b59d85c3c3f4fec5813	95eff9fe07da75fb710212a91f1d656a	a5g2dabg68jm64kbtbaf8	1	\N	\N	2026-01-01 14:01:39.685	2026-01-01 14:02:03.129432
68363029973923abb395c9d84ed56395	95eff9fe07da75fb710212a91f1d656a	wwodbpy3ivehahqbpqzplu	1	\N	\N	2026-01-01 14:01:37.953	2026-01-01 14:02:03.197032
f374e2ef6acd8060582c0f1fe2b23fbd	95eff9fe07da75fb710212a91f1d656a	lnc9zpltipgc0bml7sjn6	1	\N	\N	2026-01-01 14:01:37.034	2026-01-01 14:02:03.26812
beb5b4a1dca03a1e904e3bd389290b49	95eff9fe07da75fb710212a91f1d656a	06j9c51lycgaekr3nsdpuzh	1	\N	\N	2026-01-01 14:01:47.107	2026-01-01 14:02:03.332355
1ba1f39aa25ad2973502c288aa5549a5	95eff9fe07da75fb710212a91f1d656a	01jcizn5pqezmw95e169c7	1	\N	\N	2026-01-01 14:01:48.686	2026-01-01 14:02:03.39825
1a37273cbe9a2b33fa54599235242a57	95eff9fe07da75fb710212a91f1d656a	672rzmhqs2elm9gtsddnwn	1	\N	\N	2026-01-01 14:01:49.927	2026-01-01 14:02:03.453702
2e01960b186aff3050177a048d234134	95eff9fe07da75fb710212a91f1d656a	6nmqspsgxi778ggmebyt5	1	\N	\N	2026-01-01 14:01:55.741	2026-01-01 14:02:03.514317
613066362c688f14061b41a9fac2195c	95eff9fe07da75fb710212a91f1d656a	irwo42pxygdkkp5q94rne	1	\N	\N	2026-01-01 14:01:57.408	2026-01-01 14:02:03.580677
\.


--
-- Data for Name: assessments; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.assessments (id, "userId", "isoStandardId", title, status, "complianceScore", "maturityLevel", "completedAt", "createdAt", "updatedAt") FROM stdin;
ec4dc0072a9d517d4899a74be2125625	ea99a5c24cf7f11e85e690d3ddd78a83	00d7aa7639f38f9892b5f763a3a9e8d1	\N	IN_PROGRESS	\N	\N	\N	2025-12-29 03:37:03.911	2025-12-29 03:37:03.911
62c409ea154e0e500ed98443902f3cb6	ea99a5c24cf7f11e85e690d3ddd78a83	00d7aa7639f38f9892b5f763a3a9e8d1	\N	IN_PROGRESS	\N	\N	\N	2025-12-29 03:37:29.974	2025-12-29 03:37:29.974
825d444c90afd64fb97017744a90d376	acfbe95d6945ffe66f39530e0a0b7006	iso9001-2015	\N	IN_PROGRESS	\N	\N	\N	2025-12-29 12:38:13.62	2025-12-29 12:38:13.62
19bdbf99e6794bbc7c27f8f3f8f315f2	acfbe95d6945ffe66f39530e0a0b7006	iso9001-2015	\N	IN_PROGRESS	\N	\N	\N	2025-12-29 13:00:51.507	2025-12-29 13:00:51.507
8e4f8bc566a44dfe27a95b7bf70c994d	ea99a5c24cf7f11e85e690d3ddd78a83	iso9001-2015	\N	IN_PROGRESS	\N	\N	\N	2025-12-29 13:13:34.067	2025-12-29 13:13:34.067
f4aa5b1635a8c36709d45b9d5c386f33	acfbe95d6945ffe66f39530e0a0b7006	iso9001-2015	\N	COMPLETED	68.00	Medium	2025-12-29 23:43:55.334	2025-12-29 13:22:51.542	2025-12-29 18:43:55.456611
07eebf55e5695e59b9050b2e2cbda760	32839eee11666cb6a0cdd4b3bffacfe2	iso9001-2015	\N	IN_PROGRESS	\N	\N	\N	2025-12-30 04:17:32.955	2025-12-30 04:17:32.955
51942337b668c47200b624b162cbd3e6	32839eee11666cb6a0cdd4b3bffacfe2	iso9001-2015	\N	COMPLETED	56.00	Medium	2025-12-30 09:22:38.96	2025-12-30 04:19:49.876	2025-12-30 04:22:42.504162
dd0b95d5ec546b9d327057f46d40c960	acfbe95d6945ffe66f39530e0a0b7006	kw-iso-combined-v1	\N	IN_PROGRESS	\N	\N	\N	2026-01-01 12:19:04.611	2026-01-01 12:19:04.611
95eff9fe07da75fb710212a91f1d656a	acfbe95d6945ffe66f39530e0a0b7006	kw-iso-combined-v1	\N	COMPLETED	11.03	Ad-hoc	2026-01-01 19:02:03.587	2026-01-01 13:51:52.859	2026-01-01 14:02:03.733
3b37ff1482bc260d93a9222b8d8d988f	ea99a5c24cf7f11e85e690d3ddd78a83	kw-iso-combined-v1	\N	IN_PROGRESS	\N	\N	\N	2026-01-02 11:38:53.006	2026-01-02 11:38:53.006
1ba52eb3352913fb738f10edb1527054	acfbe95d6945ffe66f39530e0a0b7006	kw-iso-combined-v1	\N	IN_PROGRESS	\N	\N	\N	2026-01-03 04:42:13.857	2026-01-03 04:42:13.857
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.categories (id, name, slug, description, "imageUrl", "parentId", "order", active, "createdAt", "updatedAt", parentid) FROM stdin;
ae3371bfb0006fdde9e556b3ece63dd4	screens	screens	\N	\N	\N	0	t	2025-12-28 12:56:56.58	2025-12-28 12:56:56.58	\N
cat2	LCDd	lcd	\N	\N	\N	0	t	2025-12-28 14:15:12.732061	2025-12-29 06:00:13.339191	cat1
\.


--
-- Data for Name: certification_requests; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.certification_requests (id, "companyName", "contactName", "contactEmail", "contactPhone", "companySize", "currentStatus", requirements, status, "userId", "createdAt", "updatedAt", documents) FROM stdin;
\.


--
-- Data for Name: clauses; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.clauses (id, "isoStandardId", number, title, description, weight, "order", "createdAt", "updatedAt") FROM stdin;
c4	iso9001-2015	4	Context of the Organization	Understanding the organization and its context	1.00	1	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c5	iso9001-2015	5	Leadership	Leadership and commitment to the QMS	1.00	2	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c6	iso9001-2015	6	Planning	Actions to address risks and opportunities	1.00	3	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c7	iso9001-2015	7	Support	Resources, competence, awareness, communication, documented information	1.00	4	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c8	iso9001-2015	8	Operation	Operational planning and control	1.00	5	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c9	iso9001-2015	9	Performance Evaluation	Monitoring, measurement, analysis, evaluation, audit, management review	1.00	6	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
c10	iso9001-2015	10	Improvement	Nonconformity, corrective action, continual improvement	1.00	7	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
combined-clause-KW-1	kw-iso-combined-v1	KW-1	KW: PURPOSE & LEADERSHIP INTEGRITY	Evaluate the clarity of purpose and ethical conduct of leadership (Kingdom Way).	15.00	1	2026-01-01 12:15:25.017804	2026-01-01 12:15:25.017804
combined-clause-KW-2	kw-iso-combined-v1	KW-2	KW: GOVERNANCE & ACCOUNTABILITY	Assess the structure, policies, and accountability within the organisation (Kingdom Way).	15.00	2	2026-01-01 12:15:25.039647	2026-01-01 12:15:25.039647
combined-clause-KW-3	kw-iso-combined-v1	KW-3	KW: PEOPLE & WORK ENVIRONMENT	Review fairness, safety, and development of employees (Kingdom Way).	15.00	3	2026-01-01 12:15:25.057337	2026-01-01 12:15:25.057337
combined-clause-KW-4	kw-iso-combined-v1	KW-4	KW: BUSINESS PRACTICES & OPERATIONS	Evaluate honesty, pricing, supplier ethics, and quality standards (Kingdom Way).	15.00	4	2026-01-01 12:15:25.072315	2026-01-01 12:15:25.072315
combined-clause-KW-5	kw-iso-combined-v1	KW-5	KW: CUSTOMER & STAKEHOLDER RESPONSIBILITY	Assess customer feedback, marketing truthfulness, and data privacy (Kingdom Way).	15.00	5	2026-01-01 12:15:25.086066	2026-01-01 12:15:25.086066
combined-clause-KW-6	kw-iso-combined-v1	KW-6	KW: ENVIRONMENTAL & SOCIAL RESPONSIBILITY	Review environmental impact and community contribution (Kingdom Way).	15.00	6	2026-01-01 12:15:25.101062	2026-01-01 12:15:25.101062
combined-clause-KW-7	kw-iso-combined-v1	KW-7	KW: CONTINUOUS IMPROVEMENT & INNOVATION	Evaluate process review, innovation, and commitment to improvement (Kingdom Way).	10.00	7	2026-01-01 12:15:25.111417	2026-01-01 12:15:25.111417
combined-clause-ISO-4	kw-iso-combined-v1	ISO-4	ISO: Context of the Organization	Understanding the organization and its context (ISO 9001:2015).	10.00	8	2026-01-01 12:15:25.124034	2026-01-01 12:15:25.124034
combined-clause-ISO-5	kw-iso-combined-v1	ISO-5	ISO: Leadership	Leadership and commitment to the QMS (ISO 9001:2015).	10.00	9	2026-01-01 12:15:25.133549	2026-01-01 12:15:25.133549
combined-clause-ISO-6	kw-iso-combined-v1	ISO-6	ISO: Planning	Actions to address risks and opportunities (ISO 9001:2015).	10.00	10	2026-01-01 12:15:25.141748	2026-01-01 12:15:25.141748
combined-clause-ISO-7	kw-iso-combined-v1	ISO-7	ISO: Support	Resources, competence, awareness, communication, documented information (ISO 9001:2015).	10.00	11	2026-01-01 12:15:25.15283	2026-01-01 12:15:25.15283
combined-clause-ISO-8	kw-iso-combined-v1	ISO-8	ISO: Operation	Operational planning and control (ISO 9001:2015).	10.00	12	2026-01-01 12:15:25.160553	2026-01-01 12:15:25.160553
combined-clause-ISO-9	kw-iso-combined-v1	ISO-9	ISO: Performance Evaluation	Monitoring, measurement, analysis, evaluation, audit, management review (ISO 9001:2015).	10.00	13	2026-01-01 12:15:25.176517	2026-01-01 12:15:25.176517
combined-clause-ISO-10	kw-iso-combined-v1	ISO-10	ISO: Improvement	Nonconformity, corrective action, continual improvement (ISO 9001:2015).	10.00	14	2026-01-01 12:15:25.188139	2026-01-01 12:15:25.188139
\.


--
-- Data for Name: iso_settings; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.iso_settings (id, key, value, standard_id) FROM stdin;
\.


--
-- Data for Name: iso_standards; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.iso_standards (id, code, name, description, year, active, "createdAt", "updatedAt") FROM stdin;
kw-iso-combined-v1	KW-ISO-COMBINED	Integrated Kingdom Way & ISO 9001 Assessment	A comprehensive assessment combining Kingdom Way Global principles with ISO 9001:2015 Quality Management standards.	2025	t	2026-01-01 12:15:25.000366	2026-01-01 12:15:25.000366
00d7aa7639f38f9892b5f763a3a9e8d1	9001	iso	\N	2025	f	2025-12-28 13:04:55.787476	2026-01-01 12:34:03.197941
iso9001-2015	ISO 9001:2015	Quality Management Systems	Requirements for a quality management system	2015	f	2025-12-29 10:13:28.092666	2026-01-01 12:34:11.452752
\.


--
-- Data for Name: leads; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.leads (id, "userId", "isoStandardId", "companyName", "contactName", "contactEmail", "contactPhone", "companySize", "currentStatus", requirements, status, "assignedPartnerId", notes, "createdAt", "updatedAt", "lastMessageAt", "unreadMessagesCount", "companyLogo") FROM stdin;
3740a77d662bd36ed46dd979a6edac7f	acfbe95d6945ffe66f39530e0a0b7006	kw-iso-combined-v1	iworth t	lewis	lewis@gmail.com		\N	\N	\N	New	\N	\N	2026-01-01 14:19:47.394	2026-01-01 14:53:29.78663	\N	0	/uploads/a5770ede-db46-47e3-bc9f-578b0f47be10-WhatsApp-Image-2025-11-13-at-4.31.03-AM.jpeg
6de1fe99965e77b975adf93e9f345da3	b132d781d957b69f77070b3b77662043	kw-iso-combined-v1	lewis	lewis kairu	lew@gmail.com	07243333333	\N	\N	logo	New	\N	\N	2026-01-01 14:56:51.042	2026-01-01 15:02:11.496091	\N	0	/uploads/89be093a-2a6e-405c-a721-a2898801047d-WhatsApp-Image-2025-11-13-at-4.31.03-AM.jpeg
\.


--
-- Data for Name: messages; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.messages (id, "leadId", "senderId", "senderRole", message, "isInternal", "readAt", "createdAt") FROM stdin;
\.


--
-- Data for Name: nominations; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.nominations (id, "nominatorName", "nominatorEmail", "nomineeName", "nomineeEmail", "nominationType", reason, status, "createdAt", "updatedAt") FROM stdin;
7d3756ad56248458c66bb9a1fd2e0e56	lawi	l@gmail.com	iworth	\N	ORGANIZATION	bjhjdhdhjjhhjzxjhajk	NEW	2026-01-02 08:37:06.407	2026-01-02 08:37:06.407
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.order_items (id, "orderId", "productId", quantity, price) FROM stdin;
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.orders (id, "userId", "stripePaymentId", total, currency, status, "createdAt", "updatedAt") FROM stdin;
\.


--
-- Data for Name: partners; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.partners (id, name, url, logo_url, created_at) FROM stdin;
2	iworth	\N	/uploads/partners/1767113189332-WhatsApp_Image_2025-11-13_at_4.31.03_AM.jpeg	2025-12-30 11:46:29.363766
3	aposto logistics	\N	/uploads/partners/1767113634501-WhatsApp_Image_2025-12-30_at_11.39.28_AM.jpeg	2025-12-30 11:53:54.524777
\.


--
-- Data for Name: pending_orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.pending_orders (id, checkoutrequestid, merchantrequestid, userid, orderitems, total, currency, phonenumber, status, createdat, expiresat) FROM stdin;
\.


--
-- Data for Name: product_category_recommendations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_category_recommendations (id, product_id, category_id, sort_order, created_at) FROM stdin;
1	f1ef963a7aacc55277041fb794a80a87	cat2	0	2026-01-02 11:15:47.024031
\.


--
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_images (id, product_id, image_url, sort_order, created_at) FROM stdin;
1	ff5b785a82f3c77e7fa4838cbe067a91	/uploads/product_1767050064451_sh1vwyzdat.jpeg	0	2025-12-29 18:14:24.481918
2	ff5b785a82f3c77e7fa4838cbe067a91	/uploads/product_1767050064499_00bnv2anj8dfi.png	1	2025-12-29 18:14:24.500499
3	ff5b785a82f3c77e7fa4838cbe067a91	/uploads/product_1767050064502_cvvckevcpuh.png	2	2025-12-29 18:14:24.503112
4	ff5b785a82f3c77e7fa4838cbe067a91	/uploads/product_1767050064505_sq5cyopy71g.jpeg	3	2025-12-29 18:14:24.507023
5	54e5a172d92bcccfaecec4b84674727e	/uploads/product_1767051040549_a3ac5g8vtf.png	0	2025-12-29 18:30:40.589294
6	f1ef963a7aacc55277041fb794a80a87	/uploads/product_1767289650634_dwna6dff6p4.jpeg	0	2026-01-01 12:47:30.646689
\.


--
-- Data for Name: product_recommendations; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.product_recommendations (id, product_id, recommended_product_id, sort_order, created_at) FROM stdin;
1	54e5a172d92bcccfaecec4b84674727e	ff5b785a82f3c77e7fa4838cbe067a91	0	2025-12-29 18:30:03.95466
2	ff5b785a82f3c77e7fa4838cbe067a91	54e5a172d92bcccfaecec4b84674727e	0	2025-12-29 18:33:27.623546
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.products (id, name, description, price, currency, sku, type, "fileUrl", imageurl, active, "createdAt", "updatedAt", "categoryId", stock, maincategoryid, subcategoryid, previousprice, specialprice, specialevent, specialactive, specialstart, specialend) FROM stdin;
54e5a172d92bcccfaecec4b84674727e	screens	screens 	3000.00	USD	SKU-1767001341840-LYEPPY	digital	\N	/uploads/product_1767001341939_r7ddpuyy2i.png	t	2025-12-29 04:42:21.957858	2025-12-29 18:30:40.616704	\N	20	ae3371bfb0006fdde9e556b3ece63dd4	\N	\N	\N	\N	f	\N	\N
ff5b785a82f3c77e7fa4838cbe067a91	screen	20inch 	3005503.00	USD	SKU-1767001537710-1XFIE3	digital	\N	/uploads/product_1767005943772_i927uwfgoc.png	t	2025-12-29 04:45:40.245476	2025-12-29 18:32:54.310103	\N	270	cat2	\N	\N	\N	\N	f	\N	\N
f1ef963a7aacc55277041fb794a80a87	car	hreihgeqh	1112121.00	USD	SKU-1767289650569-ZDV7VJ	physical	\N	\N	t	2026-01-01 12:47:30.627172	2026-01-02 11:15:46.220831	\N	200	cat2	\N	\N	\N	\N	f	\N	\N
\.


--
-- Data for Name: questions; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.questions (id, "clauseId", text, description, type, options, weight, "order", required, "createdAt", "updatedAt") FROM stdin;
q4_1	c4	Has the organization identified internal and external issues relevant to its purpose and strategic direction?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q4_2	c4	Are the needs and expectations of interested parties determined and reviewed?	\N	YES_NO	\N	1.00	2	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q4_3	c4	Is the scope of the QMS documented and maintained?	\N	YES_NO	\N	1.00	3	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q5_1	c5	Has top management demonstrated leadership and commitment to the QMS?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q5_2	c5	Is a quality policy established, implemented, and communicated?	\N	YES_NO	\N	1.00	2	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q5_3	c5	Are roles, responsibilities, and authorities assigned and communicated?	\N	YES_NO	\N	1.00	3	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q6_1	c6	Are risks and opportunities that could affect the QMS identified and addressed?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q6_2	c6	Are quality objectives established at relevant functions and levels?	\N	YES_NO	\N	1.00	2	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q6_3	c6	Are changes to the QMS planned and managed?	\N	YES_NO	\N	1.00	3	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q7_1	c7	Are resources determined and provided for the QMS?	\N	YES_NO	\N	1.00	1	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q7_2	c7	Are personnel competent based on education, training, or experience?	\N	YES_NO	\N	1.00	2	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q7_3	c7	Are employees aware of the quality policy and their contribution to the QMS?	\N	YES_NO	\N	1.00	3	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q7_4	c7	Is documented information controlled and maintained?	\N	YES_NO	\N	1.00	4	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_1	c8	Are processes for operational planning and control implemented?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_2	c8	Are customer requirements for products and services determined and reviewed?	\N	YES_NO	\N	1.00	2	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_3	c8	Is design and development controlled (if applicable)?	\N	YES_NO	\N	1.00	3	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_4	c8	Are controls in place for externally provided processes, products, and services?	\N	YES_NO	\N	1.00	4	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_5	c8	Is production and service provision carried out under controlled conditions?	\N	YES_NO	\N	1.00	5	f	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_6	c8	Are products and services released only after meeting requirements?	\N	YES_NO	\N	1.00	6	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q8_7	c8	Are nonconforming outputs identified and controlled?	\N	YES_NO	\N	1.00	7	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q9_1	c9	Are monitoring, measurement, analysis, and evaluation activities conducted?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q9_2	c9	Are internal audits planned and conducted?	\N	YES_NO	\N	1.00	2	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q9_3	c9	Does management review the QMS at planned intervals?	\N	YES_NO	\N	1.00	3	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q10_1	c10	Are nonconformities and corrective actions managed?	\N	YES_NO	\N	1.00	1	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
q10_2	c10	Is there evidence of continual improvement of the QMS?	\N	YES_NO	\N	1.00	2	t	2025-12-29 10:13:28.092666	2025-12-29 10:13:28.092666
srskaxtiqcfjuv8by8vw88	combined-clause-KW-1	Our organisation has a clearly defined purpose beyond profit.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.023554	2026-01-01 12:15:25.023554
3swcs2dgct92tqct12vk4k	combined-clause-KW-1	Senior leadership demonstrates ethical conduct in decision-making.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.027794	2026-01-01 12:15:25.027794
mensmr1fohekeas60e4rz8	combined-clause-KW-1	Leadership behaviours are consistent with stated values.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.030597	2026-01-01 12:15:25.030597
95ppm4obxjapsbe0zanyc9	combined-clause-KW-1	Ethical leadership expectations are communicated across the organisation.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.033773	2026-01-01 12:15:25.033773
alzubkpfnst0uwayiuniu4s	combined-clause-KW-1	Conflicts of interest are disclosed and managed transparently.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.036729	2026-01-01 12:15:25.036729
vja0lqg7y1oy48fk75v6gc	combined-clause-KW-2	Roles, responsibilities, and authority levels are clearly defined.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.042618	2026-01-01 12:15:25.042618
xma53nga6396ko6sfzxfma	combined-clause-KW-2	The organisation has documented policies guiding ethical conduct.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.04676	2026-01-01 12:15:25.04676
ys9lbdlcb99tr3m24g9o	combined-clause-KW-2	Decisions are recorded and traceable.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.051498	2026-01-01 12:15:25.051498
3yo6a79rhbegcp8r8uq7yi	combined-clause-KW-2	Performance is reviewed against agreed objectives.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.05406	2026-01-01 12:15:25.05406
22wqksbbnvqis8jr22afsjl	combined-clause-KW-2	Leadership is accountable for organisational outcomes.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.05582	2026-01-01 12:15:25.05582
wc0bw5redk5t19bm4ng	combined-clause-KW-3	Employees are treated fairly and respectfully.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.05959	2026-01-01 12:15:25.05959
9whvxpv75ofvxxyxic6c7	combined-clause-KW-3	The organisation promotes a safe and healthy work environment.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.061607	2026-01-01 12:15:25.061607
pc2aowfti62zxgq6piiut	combined-clause-KW-3	Equal opportunity and inclusion are actively supported.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.064735	2026-01-01 12:15:25.064735
fk1xlwzokimp5rvcpg2dop	combined-clause-KW-3	Staff development and capacity building are prioritised.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.068641	2026-01-01 12:15:25.068641
89nd28dz5ybw57r7wm1lx	combined-clause-KW-3	Workplace grievances are handled transparently and fairly.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.070996	2026-01-01 12:15:25.070996
hoc52w2etydetdlfvpz8lw	combined-clause-KW-4	Products or services are delivered honestly and as promised.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.073526	2026-01-01 12:15:25.073526
0nuho526fvmghe1e73sbt5	combined-clause-KW-4	Pricing is fair, transparent, and justifiable.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.075018	2026-01-01 12:15:25.075018
kgn2jku42jryy23wlb82s	combined-clause-KW-4	Suppliers and partners are selected ethically.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.076358	2026-01-01 12:15:25.076358
nge0uunzdmoejzyxf7j7e	combined-clause-KW-4	Business risks are identified and managed responsibly.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.077753	2026-01-01 12:15:25.077753
6f3b0uyirnsovx7u0myll	combined-clause-KW-4	Quality standards are consistently applied.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.081834	2026-01-01 12:15:25.081834
z8cbnifd2vsz2y0f71rfgg	combined-clause-KW-5	Customer feedback is actively collected and addressed.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.088258	2026-01-01 12:15:25.088258
z00i700mhvnd4ofn88zj	combined-clause-KW-5	Marketing and communication are truthful and responsible.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.089603	2026-01-01 12:15:25.089603
397brzl7kmpt93v169girj	combined-clause-KW-5	Stakeholders are treated with respect and professionalism.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.091009	2026-01-01 12:15:25.091009
23a8sx7d8acfwag9qf0or5	combined-clause-KW-5	Data and privacy are protected appropriately.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.092644	2026-01-01 12:15:25.092644
dzysvuhcwkg2aq2blmc7re	combined-clause-KW-5	Complaints are resolved fairly and timely.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.094972	2026-01-01 12:15:25.094972
hl8i8bf4gnwxorb84uelgb	combined-clause-KW-6	The organisation considers environmental impact in its operations.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.103572	2026-01-01 12:15:25.103572
rn1ojzoihhg3clx7ohk58	combined-clause-KW-6	Waste reduction or responsible disposal practices are in place.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.104935	2026-01-01 12:15:25.104935
rxjsj0w3tekzviv471auu	combined-clause-KW-6	Resource use (energy, water, materials) is monitored or managed.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.105876	2026-01-01 12:15:25.105876
pg42ma61a6muggj8o7r7kc	combined-clause-KW-6	The organisation contributes positively to its community.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.107582	2026-01-01 12:15:25.107582
2ku501rpbhlyj9irir8eqq	combined-clause-KW-6	Long-term sustainability is part of business planning.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.109475	2026-01-01 12:15:25.109475
vsqaw18d4wm315955pwhlq	combined-clause-KW-7	The organisation regularly reviews its processes.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.114501	2026-01-01 12:15:25.114501
q1949rj0nwu4z5vm5qpbc	combined-clause-KW-7	Lessons learned are documented and applied.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.116494	2026-01-01 12:15:25.116494
rev6g6t5bswq902tocedl	combined-clause-KW-7	Innovation is encouraged ethically and responsibly.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.119658	2026-01-01 12:15:25.119658
c6h286ln5xg1v4m9hgphpp	combined-clause-KW-7	Compliance requirements are monitored.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.121758	2026-01-01 12:15:25.121758
pg2g121k0y9uzb32gprbu	combined-clause-KW-7	The organisation is committed to improvement over time.	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.122901	2026-01-01 12:15:25.122901
iwm6ytxvm7krxkb9m7gfqs	combined-clause-ISO-4	Has the organization identified internal and external issues relevant to its purpose and strategic direction?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.126001	2026-01-01 12:15:25.126001
jwi9p6w2ism681gkf50prt	combined-clause-ISO-4	Are the needs and expectations of interested parties determined and reviewed?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.127621	2026-01-01 12:15:25.127621
t5dedt02uzmxgc8m9jm3	combined-clause-ISO-4	Is the scope of the QMS documented and maintained?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.130759	2026-01-01 12:15:25.130759
1o3sje2xo6al33x9bkobc	combined-clause-ISO-5	Has top management demonstrated leadership and commitment to the QMS?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.135648	2026-01-01 12:15:25.135648
4wqj377hljv0h9grw0wobk	combined-clause-ISO-5	Is a quality policy established, implemented, and communicated?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.137697	2026-01-01 12:15:25.137697
v1w594veett0esgiw0bq	combined-clause-ISO-5	Are roles, responsibilities, and authorities assigned and communicated?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.139891	2026-01-01 12:15:25.139891
as6m3c746nef0oud61gu8	combined-clause-ISO-6	Are risks and opportunities that could affect the QMS identified and addressed?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.143371	2026-01-01 12:15:25.143371
hm7jwsduuwpigzw5oogp	combined-clause-ISO-6	Are quality objectives established at relevant functions and levels?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.145333	2026-01-01 12:15:25.145333
l1bk2jutz7cpnwutrpl5qp	combined-clause-ISO-6	Are changes to the QMS planned and managed?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.148634	2026-01-01 12:15:25.148634
8ehwl6kjfzuvjnvbdqbcca	combined-clause-ISO-7	Are resources determined and provided for the QMS?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.154632	2026-01-01 12:15:25.154632
bh108qb0yh5m4a4s5ikn4	combined-clause-ISO-7	Are personnel competent based on education, training, or experience?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.15575	2026-01-01 12:15:25.15575
sekqj60xrps9n1wb52wnf	combined-clause-ISO-7	Are employees aware of the quality policy and their contribution to the QMS?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.156778	2026-01-01 12:15:25.156778
i7qnxaod0qqun82i1e5k	combined-clause-ISO-7	Is documented information controlled and maintained?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.158563	2026-01-01 12:15:25.158563
rr3z81ut2up55z4pxrax9o	combined-clause-ISO-8	Are processes for operational planning and control implemented?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.163099	2026-01-01 12:15:25.163099
v7qo1tt4nk01siewkr9jtc	combined-clause-ISO-8	Are customer requirements for products and services determined and reviewed?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.165422	2026-01-01 12:15:25.165422
w7ipjp447tmrbiya1okcv	combined-clause-ISO-8	Is design and development controlled (if applicable)?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.167698	2026-01-01 12:15:25.167698
3r5jen1qwsmqx68y6fji1	combined-clause-ISO-8	Are controls in place for externally provided processes, products, and services?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	4	t	2026-01-01 12:15:25.17065	2026-01-01 12:15:25.17065
a5g2dabg68jm64kbtbaf8	combined-clause-ISO-8	Is production and service provision carried out under controlled conditions?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	5	t	2026-01-01 12:15:25.172273	2026-01-01 12:15:25.172273
wwodbpy3ivehahqbpqzplu	combined-clause-ISO-8	Are products and services released only after meeting requirements?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	6	t	2026-01-01 12:15:25.173227	2026-01-01 12:15:25.173227
lnc9zpltipgc0bml7sjn6	combined-clause-ISO-8	Are nonconforming outputs identified and controlled?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	7	t	2026-01-01 12:15:25.174606	2026-01-01 12:15:25.174606
06j9c51lycgaekr3nsdpuzh	combined-clause-ISO-9	Are monitoring, measurement, analysis, and evaluation activities conducted?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.18061	2026-01-01 12:15:25.18061
01jcizn5pqezmw95e169c7	combined-clause-ISO-9	Are internal audits planned and conducted?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.184028	2026-01-01 12:15:25.184028
672rzmhqs2elm9gtsddnwn	combined-clause-ISO-9	Does management review the QMS at planned intervals?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	3	t	2026-01-01 12:15:25.186481	2026-01-01 12:15:25.186481
6nmqspsgxi778ggmebyt5	combined-clause-ISO-10	Are nonconformities and corrective actions managed?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	1	t	2026-01-01 12:15:25.189516	2026-01-01 12:15:25.189516
irwo42pxygdkkp5q94rne	combined-clause-ISO-10	Is there evidence of continual improvement of the QMS?	\N	SCALE	{"max": 3, "min": 0, "labels": ["Not in place", "Informal", "Partially", "Fully"]}	1.00	2	t	2026-01-01 12:15:25.190631	2026-01-01 12:15:25.190631
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.sessions (id, "sessionToken", "userId", expires) FROM stdin;
\.


--
-- Data for Name: site_settings; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.site_settings (id, key, value, "createdAt", "updatedAt", category, type, label, description, ispublic, requiresrestart, createdat, updatedat, currency, currencysymbol, inventoryenabled, lowstockthreshold) FROM stdin;
c8ef83523cd0473348cee03f684a9038	theme_success_color	#0F766E	2025-12-28 12:00:18.203366	2026-01-02 10:28:13.464541	\N	string	\N	\N	f	f	2025-12-28 12:00:18.203366	2025-12-28 12:00:18.203366	USD	$	t	5
4954a552-5549-4c92-b3da-aae700b9a228	login_attempt_limit	5	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	security	number	Login Attempt Limit	Max failed login attempts before lockout	f	f	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	USD	$	t	5
72007864-ec76-4085-8b3a-106962b8a0af	login_lockout_duration	900	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	security	number	Lockout Duration (seconds)	Time to lock account after failed attempts (15 minutes default)	f	f	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	USD	$	t	5
7e9fda67-07d5-4220-b92c-52b6dc399e7c	api_rate_limit	100	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	security	number	API Rate Limit	API requests per minute per IP	f	f	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	USD	$	t	5
3d53e365-c20c-464e-835c-2810699e69df	force_password_reset_days	90	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	security	number	Force Password Reset (days)	Days before forcing password reset	f	f	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	USD	$	t	5
d2bd791a-4cb2-4444-821b-02718c8b7721	audit_log_retention_days	365	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	security	number	Audit Log Retention (days)	Days to retain audit logs	f	f	2025-12-28 12:21:38.714403	2025-12-28 12:21:38.714403	USD	$	t	5
55c20433-4d85-4edc-a310-a1584793f1b3	contact_email		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.426576	general	string	Contact Email	Main contact email address displayed in footer	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
e56a5e38-5096-49ef-869b-7a0dac43df31	contact_phone	0723849943	2025-12-30 15:30:36.22477	2026-01-02 10:28:13.430173	general	string	Contact Phone	Main contact phone number displayed in footer	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
b1c70433-4344-4a28-80a0-bf48337d87d3	social_facebook		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.432457	general	string	Facebook URL	Your Facebook page/profile URL	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
2acdac98-eb32-4d75-a28b-43b838d33ffe	social_instagram		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.434176	general	string	Instagram URL	Your Instagram profile URL	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
9f7129ee-86eb-439e-aaf7-5e835f5772d9	social_linkedin		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.437747	general	string	LinkedIn URL	Your LinkedIn company/profile URL	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
88c79174-3139-4c0d-9764-b2830a42c40c	social_twitter		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.440589	general	string	Twitter URL	Your Twitter/X profile URL	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
5236ca88-85e9-4387-af8f-41e95eab203a	social_youtube		2025-12-30 15:30:36.22477	2026-01-02 10:28:13.442645	general	string	YouTube URL	Your YouTube channel URL	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
7a9ee3a9-4726-48a4-b3d0-e2d290922e7c	company_logo	/uploads/upload_1767296418660_y0c8b3.jpeg	2025-12-27 09:44:56.424723	2026-01-02 10:28:13.444314	\N	string	\N	\N	f	f	2025-12-28 11:37:58.362123	2025-12-28 11:37:58.362123	USD	$	t	5
992ca477-51d2-4029-a07b-7965bedc10d7	company_name	Kingdom Way Global 	2025-12-27 09:44:56.424723	2026-01-02 10:28:13.44684	\N	string	\N	\N	f	f	2025-12-28 11:37:58.362123	2025-12-28 11:37:58.362123	USD	$	t	5
de20c4e7-c652-4ce1-b631-9ce8feb486d1	footer_text	© 2024 Kingdom Way Global Organization All rights reserved.	2025-12-27 09:44:56.424723	2026-01-02 10:28:13.448696	\N	string	\N	\N	f	f	2025-12-28 11:37:58.362123	2025-12-28 11:37:58.362123	USD	$	t	5
3073ca081ce41cd11965f24a05dbece2	theme_accent_color	#7C3AED	2025-12-28 12:00:18.206254	2026-01-02 10:28:13.450304	\N	string	\N	\N	f	f	2025-12-28 12:00:18.206254	2025-12-28 12:00:18.206254	USD	$	t	5
993c8b785da6093318de5070c27d0958	theme_background_color	#FAFBFC	2025-12-28 12:00:18.201761	2026-01-02 10:28:13.452501	\N	string	\N	\N	f	f	2025-12-28 12:00:18.201761	2025-12-28 12:00:18.201761	USD	$	t	5
1ba387df2a4cceb0884d837584c25779	theme_error_color	#DC2626	2025-12-28 12:00:18.208629	2026-01-02 10:28:13.455872	\N	string	\N	\N	f	f	2025-12-28 12:00:18.208629	2025-12-28 12:00:18.208629	USD	$	t	5
33da05d4676c17d258e81be7ec890c95	theme_primary_color	#475569	2025-12-28 12:00:18.193421	2026-01-02 10:28:13.458514	\N	string	\N	\N	f	f	2025-12-28 12:00:18.193421	2025-12-28 12:00:18.193421	USD	$	t	5
f3a697a1-fd67-45d1-9d48-f697b4e83731	contact_address	coffee Plaza ,6th floor,off Haile Sellasie Avenue -Nairobi	2025-12-30 15:30:36.22477	2026-01-02 10:28:13.408479	general	text	Contact Address	Physical address displayed in footer	f	f	2025-12-30 15:30:36.22477	2025-12-30 15:30:36.22477	USD	$	t	5
f6337d8db54329a96e66adce3a6dbd33	theme_primary_hover_color	#334155	2025-12-28 12:00:18.198281	2026-01-02 10:28:13.460765	\N	string	\N	\N	f	f	2025-12-28 12:00:18.198281	2025-12-28 12:00:18.198281	USD	$	t	5
cbcfa39cd33486a90e146cdb691e14f4	theme_primary_soft_color	#F1F5F9	2025-12-28 12:00:18.200068	2026-01-02 10:28:13.46277	\N	string	\N	\N	f	f	2025-12-28 12:00:18.200068	2025-12-28 12:00:18.200068	USD	$	t	5
cd15c5e2-21fe-4f0a-a69a-b3b73469eef2	registration_enabled	true	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Enable Registration	Allow new users to register	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
70f2845a-113c-47db-b050-514be5613aaa	default_user_role	USER	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	string	Default User Role	Role assigned to new users	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
5e1faf5b-e738-4064-872e-8d0291bc4646	password_min_length	8	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	number	Minimum Password Length	Minimum characters required for passwords	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
d1e71dcc-00f8-4a25-8c17-e26e3593b207	password_require_uppercase	true	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Require Uppercase	Passwords must contain uppercase letters	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
b56e7d55-c4b6-4943-93e2-663a1627a185	password_require_lowercase	true	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Require Lowercase	Passwords must contain lowercase letters	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
efcefbd6-e441-445b-9004-d62503d1c4e3	password_require_numbers	true	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Require Numbers	Passwords must contain numbers	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
1c2fb450-f2e5-4c88-88c1-7fd89ea9523f	password_require_special	false	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Require Special Characters	Passwords must contain special characters	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
dbfe0b20-b001-419f-b5a8-1de5fb429fb3	session_timeout	3600	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	number	Session Timeout (seconds)	Time before user session expires (in seconds)	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
00308686-cd2a-4aaa-be21-0c7b5ad16d21	two_factor_enabled	false	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Enable Two-Factor Authentication	Require 2FA for admin accounts	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
b8dd6353-730d-4980-80d1-3b3766243f2f	email_verification_required	false	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	users	boolean	Require Email Verification	Users must verify email before accessing the platform	f	f	2025-12-28 12:23:36.336909	2025-12-28 12:23:36.336909	USD	$	t	5
8e4d8a36-c61e-4b3c-ab7e-1550215cefd6	sku_format	SKU-{id}	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	inventory	string	SKU Format	Format for generating SKUs (use {id} for product ID)	f	f	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	USD	$	t	5
e4da17c4-5e5e-4553-904b-edd16906b144	low_stock_threshold	10	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	inventory	number	Low Stock Threshold	Alert when stock falls below this number	f	f	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	USD	$	t	5
32ecf937-0a89-412e-bdce-f7f5813be049	stock_reservation_timeout	900	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	inventory	number	Stock Reservation Timeout (seconds)	Time to hold reserved stock (15 minutes default)	f	f	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	USD	$	t	5
e62a3e33-9d49-4c32-8710-757669ed0b70	stock_alerts_enabled	true	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	inventory	boolean	Enable Stock Alerts	Send alerts when stock is low	f	f	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	USD	$	t	5
d1d52f2b-b18d-44a8-bedb-dedcf15fde6e	inventory_audit_frequency	monthly	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	inventory	string	Inventory Audit Frequency	How often to perform inventory audits (daily/weekly/monthly)	f	f	2025-12-28 12:23:36.341089	2025-12-28 12:23:36.341089	USD	$	t	5
eaf21276-a30f-44f5-9da4-5fb82c571aa2	iso_scoring_mode	auto	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	iso_compliance	string	Scoring Mode	Scoring calculation mode: auto, manual, or hybrid	f	f	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	USD	$	t	5
caeae143-088a-460d-bcdc-bbc6a7a62111	iso_readiness_threshold	70	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	iso_compliance	number	Readiness Threshold (%)	Minimum score percentage for ISO readiness	f	f	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	USD	$	t	5
42befabb-d382-4870-bab8-79f5fdc9353c	certificate_validity_days	365	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	iso_compliance	number	Certificate Validity (days)	Number of days certificates remain valid	f	f	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	USD	$	t	5
1a96f13e-bd21-4d98-b7c6-54305a0e4461	audit_workflow_stages	["preparation","planning","execution","reporting","followup"]	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	iso_compliance	json	Audit Workflow Stages	JSON array of audit workflow stages	f	f	2025-12-28 12:23:36.344083	2025-12-28 12:23:36.344083	USD	$	t	5
5865cba6-ba73-4a3b-b5ab-57146e4387a6	currency_symbol	ksh	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.968318	ecommerce	string	Currency Symbol	Currency symbol to display	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
b711bfc0-7124-4391-8c47-026661df6b5d	invoice_prefix	INV-	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.970442	ecommerce	string	Invoice Prefix	Prefix for invoice numbers	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
8aeb6f1e-c7b1-43e7-8008-ae5f71e687c1	order_status_workflow	["pending","processing","shipped","delivered","cancelled"]	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.972503	ecommerce	json	Order Status Workflow	JSON array of order statuses in workflow order	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
11de78e8-3f02-4cde-9971-a71443b31a6b	payment_paypal_enabled	false	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.975054	ecommerce	boolean	Enable PayPal Payments	Allow PayPal payment processing	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
63bb5cc1-fc10-40ae-9b90-76d0ffa5f36b	payment_stripe_enabled	false	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.979243	ecommerce	boolean	Enable Stripe Payments	Allow Stripe payment processing	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
56bd067a-3b9b-47f2-bbdf-404364512b1c	tax_rate	0	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.982402	ecommerce	number	Tax Rate	Default tax rate (as decimal, e.g., 0.08 for 8%)	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
30fcd946-090d-4d29-b5f5-82f27b1152c5	document_max_size_mb	10	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	number	Max Upload Size (MB)	Maximum file size for document uploads	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
cdacb3bf-04c4-41b6-a724-4b2dd7df605d	document_allowed_types	["pdf","doc","docx","xls","xlsx","png","jpg","jpeg"]	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	json	Allowed File Types	JSON array of allowed file extensions	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
67b11054-cece-4790-9510-86658708222b	document_storage_location	local	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	string	Storage Location	Storage backend: local or cloud	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
c4e3e112-f746-4fb9-9f15-87a688462e8b	document_versioning_enabled	true	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	boolean	Enable Versioning	Keep version history for documents	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
2fa27c2e-369d-4677-9279-30ed7743137e	document_retention_days	365	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	number	Retention Period (days)	Days to retain documents before deletion	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
ed71f4de-5873-497f-9af1-2edb858578e2	document_expiry_reminder_days	30	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	documents	number	Expiry Reminder (days)	Days before expiry to send reminder	f	f	2025-12-28 12:23:36.345199	2025-12-28 12:23:36.345199	USD	$	t	5
f8cd35cd-3b4a-4794-85d9-8bbb6d869b85	notifications_email_enabled	true	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	notifications	boolean	Enable Email Notifications	Send notifications via email	f	f	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	USD	$	t	5
5e4ff531-7e83-4061-80ed-33ce521cd58d	notifications_sms_enabled	false	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	notifications	boolean	Enable SMS Notifications	Send notifications via SMS	f	f	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	USD	$	t	5
9afee316-c3e1-4509-9e44-cb089bcaf9e6	admin_alert_threshold	5	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	notifications	number	Admin Alert Threshold	Number of issues before admin alert	f	f	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	USD	$	t	5
f7a19adc-e4b0-48ab-b55f-19fe2f638cd9	daily_summary_enabled	false	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	notifications	boolean	Enable Daily Summary	Send daily summary emails to admins	f	f	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	USD	$	t	5
e206ef31-874d-4012-836a-b48ebd08266c	weekly_summary_enabled	true	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	notifications	boolean	Enable Weekly Summary	Send weekly summary emails to admins	f	f	2025-12-28 12:23:36.346917	2025-12-28 12:23:36.346917	USD	$	t	5
9426fdba-3c58-484d-9539-ee96d37090bf	email_smtp_host		2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	SMTP Host	SMTP server hostname	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
22414bbc-3554-43b2-be3e-938786b91db7	email_smtp_port	587	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	number	SMTP Port	SMTP server port	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
6628e89e-ca5e-46be-98d6-59112d5a9f83	email_smtp_user		2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	SMTP Username	SMTP authentication username	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
09251885-6cc7-4ac8-abaf-401d8493562f	email_smtp_password		2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	SMTP Password	SMTP authentication password (encrypted)	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
da73632e-a52d-47c7-976d-7656ffbc2459	email_from_address		2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	From Email Address	Default sender email address	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
0b9767ae-68c5-4b26-aa54-adb08571c733	sms_provider		2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	SMS Provider	SMS service provider (twilio, etc.)	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
fc6794fc-efee-4ed1-956f-1497bc54f6ec	storage_provider	local	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	integrations	string	Storage Provider	Cloud storage provider (aws, gcp, azure, local)	f	f	2025-12-28 12:23:36.350158	2025-12-28 12:23:36.350158	USD	$	t	5
29683a64-02b4-448a-b0fc-c9f19603b2e6	currency	ksh	2025-12-28 12:23:36.342327	2025-12-28 13:57:09.950617	ecommerce	string	Currency	Default currency code (ISO 4217)	f	f	2025-12-28 12:23:36.342327	2025-12-28 12:23:36.342327	USD	$	t	5
\.


--
-- Data for Name: standards; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.standards (id, name, description, version, created_at) FROM stdin;
\.


--
-- Data for Name: terms_and_conditions; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.terms_and_conditions (id, title, content, version, is_active, created_at) FROM stdin;
1	ISO Compliance Platform Terms & Conditions	By using this platform, you agree to:\\n\\n1. Provide accurate and truthful information during all assessments.\\n2. Upload only genuine and relevant evidence documents when required.\\n3. Understand that self-assessment does not constitute official ISO certification.\\n4. Accept that all assessment data may be reviewed by platform administrators for compliance and quality.\\n5. Acknowledge that your assessment results are provisional until reviewed and approved by an authorized ISO officer.\\n6. Comply with all applicable laws and regulations regarding data privacy and information security.\\n7. Accept that the platform may update these terms and require re-acceptance for continued use.\\n\\nDisclaimer: This self-assessment tool is for internal readiness and gap analysis only. Final ISO certification requires an independent accredited audit.	1.0	t	2025-12-29 06:20:36.658209
\.


--
-- Data for Name: user_answers; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_answers (id, assessment_id, question_id, answer, evidence_url, evidence_status) FROM stdin;
\.


--
-- Data for Name: user_assessments; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_assessments (id, user_id, standard_id, standard_version, started_at, completed_at, status, score, maturity_level) FROM stdin;
\.


--
-- Data for Name: user_terms_acceptances; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.user_terms_acceptances (id, user_id, terms_id, accepted_at, ip_address, user_agent) FROM stdin;
1	acfbe95d-6945-ffe6-6f39-530e0a0b7006	1	2025-12-29 16:29:17.803075	::ffff:127.0.0.1	Mozilla/5.0 (X11; Linux x86_64; rv:128.0) Gecko/20100101 Firefox/128.0
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.users (id, email, "emailVerified", password, name, image, role, "createdAt", "updatedAt") FROM stdin;
183e705b61e924dde99cf5c211a53b2e	kairu@gmail.com	\N	$2a$12$/UCDmWYRRN55WZAyZ21xhOJxmmBQpUS/bESs3L3.x3vQWmfXx9Dku	kairu	\N	ADMIN	2025-12-27 10:25:25.525	2025-12-28 13:32:00.041744
86734699f0ca01ab73d485c0a88384bb	v@gmail.com	\N	$2a$12$YvAwSq/QHF/n/i9nV8S7.uax2zhJpcZ95HmGX1/2fFesENh.5l4GO	vybz	\N	USER	2025-12-29 14:27:23.426	2025-12-29 14:27:23.426
32839eee11666cb6a0cdd4b3bffacfe2	microadsales@gmail.com	\N	$2a$12$6hgJMKrZHT7PkhVBk1jhOue2VWRNv8Z3NuGHiLkUhanr2xjLWNESi	Microad	\N	USER	2025-12-30 04:16:00.597	2025-12-30 04:16:00.597
acfbe95d6945ffe66f39530e0a0b7006	lewis@gmail.com	\N	$2a$12$1iempxoFal1mqg5LILxcj.US3GQiq7ciAgmKpCEplVSqsPdj0qOJm	lewis	\N	ADMIN	2025-12-27 09:32:11.807	2026-01-01 14:53:29.758324
b132d781d957b69f77070b3b77662043	lew@gmail.com	\N	$2a$12$gl//pUd64o5u20IhXv1fBO0/EKN7aDSsp0r40RLmCcBxPtQUOrWTy	lewis kairu	\N	USER	2026-01-01 14:56:51.026	2026-01-01 15:02:11.473518
ea99a5c24cf7f11e85e690d3ddd78a83	kairulewis649@gmail.com	\N	$2a$12$GQ0T8RQRp1Tlun6/kGPwbuC6XvbBy3X2FyRfvz1Hlh9r3.lZxEQsC	lewis kairu	\N	ADMIN	2025-12-28 10:04:56.823	2026-01-02 10:37:18.797771
\.


--
-- Data for Name: verification_tokens; Type: TABLE DATA; Schema: public; Owner: iso_app
--

COPY public.verification_tokens (identifier, token, expires) FROM stdin;
\.


--
-- Name: about_us_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.about_us_id_seq', 1, true);


--
-- Name: iso_settings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.iso_settings_id_seq', 1, false);


--
-- Name: partners_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.partners_id_seq', 3, true);


--
-- Name: product_category_recommendations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_category_recommendations_id_seq', 1, true);


--
-- Name: product_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_images_id_seq', 6, true);


--
-- Name: product_recommendations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.product_recommendations_id_seq', 2, true);


--
-- Name: standards_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.standards_id_seq', 1, false);


--
-- Name: terms_and_conditions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.terms_and_conditions_id_seq', 1, true);


--
-- Name: user_answers_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_answers_id_seq', 200, true);


--
-- Name: user_assessments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_assessments_id_seq', 1, false);


--
-- Name: user_terms_acceptances_id_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.user_terms_acceptances_id_seq', 1, true);


--
-- Name: about_us about_us_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.about_us
    ADD CONSTRAINT about_us_pkey PRIMARY KEY (id);


--
-- Name: accounts accounts_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_pkey PRIMARY KEY (id);


--
-- Name: answers answers_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_pkey PRIMARY KEY (id);


--
-- Name: assessments assessments_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.assessments
    ADD CONSTRAINT assessments_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- Name: certification_requests certification_requests_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.certification_requests
    ADD CONSTRAINT certification_requests_pkey PRIMARY KEY (id);


--
-- Name: clauses clauses_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.clauses
    ADD CONSTRAINT clauses_pkey PRIMARY KEY (id);


--
-- Name: iso_settings iso_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.iso_settings
    ADD CONSTRAINT iso_settings_pkey PRIMARY KEY (id);


--
-- Name: iso_standards iso_standards_code_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.iso_standards
    ADD CONSTRAINT iso_standards_code_key UNIQUE (code);


--
-- Name: iso_standards iso_standards_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.iso_standards
    ADD CONSTRAINT iso_standards_pkey PRIMARY KEY (id);


--
-- Name: leads leads_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_pkey PRIMARY KEY (id);


--
-- Name: messages messages_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT messages_pkey PRIMARY KEY (id);


--
-- Name: nominations nominations_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.nominations
    ADD CONSTRAINT nominations_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: orders orders_stripepaymentid_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_stripepaymentid_key UNIQUE ("stripePaymentId");


--
-- Name: partners partners_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.partners
    ADD CONSTRAINT partners_pkey PRIMARY KEY (id);


--
-- Name: pending_orders pending_orders_checkoutrequestid_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_orders
    ADD CONSTRAINT pending_orders_checkoutrequestid_key UNIQUE (checkoutrequestid);


--
-- Name: pending_orders pending_orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_orders
    ADD CONSTRAINT pending_orders_pkey PRIMARY KEY (id);


--
-- Name: product_category_recommendations product_category_recommendations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_category_recommendations
    ADD CONSTRAINT product_category_recommendations_pkey PRIMARY KEY (id);


--
-- Name: product_category_recommendations product_category_recommendations_product_id_category_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_category_recommendations
    ADD CONSTRAINT product_category_recommendations_product_id_category_id_key UNIQUE (product_id, category_id);


--
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- Name: product_recommendations product_recommendations_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_recommendations
    ADD CONSTRAINT product_recommendations_pkey PRIMARY KEY (id);


--
-- Name: product_recommendations product_recommendations_product_id_recommended_product_id_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_recommendations
    ADD CONSTRAINT product_recommendations_product_id_recommended_product_id_key UNIQUE (product_id, recommended_product_id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_sku_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_sku_key UNIQUE (sku);


--
-- Name: questions questions_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT questions_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_sessiontoken_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_sessiontoken_key UNIQUE ("sessionToken");


--
-- Name: site_settings site_settings_key_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.site_settings
    ADD CONSTRAINT site_settings_key_key UNIQUE (key);


--
-- Name: site_settings site_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.site_settings
    ADD CONSTRAINT site_settings_pkey PRIMARY KEY (id);


--
-- Name: standards standards_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.standards
    ADD CONSTRAINT standards_pkey PRIMARY KEY (id);


--
-- Name: terms_and_conditions terms_and_conditions_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.terms_and_conditions
    ADD CONSTRAINT terms_and_conditions_pkey PRIMARY KEY (id);


--
-- Name: answers unique_assessment_question; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT unique_assessment_question UNIQUE ("assessmentId", "questionId");


--
-- Name: verification_tokens unique_identifier_token; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.verification_tokens
    ADD CONSTRAINT unique_identifier_token UNIQUE (identifier, token);


--
-- Name: accounts unique_provider_account; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT unique_provider_account UNIQUE (provider, "providerAccountId");


--
-- Name: user_answers user_answers_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_answers
    ADD CONSTRAINT user_answers_pkey PRIMARY KEY (id);


--
-- Name: user_assessments user_assessments_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_assessments
    ADD CONSTRAINT user_assessments_pkey PRIMARY KEY (id);


--
-- Name: user_terms_acceptances user_terms_acceptances_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_terms_acceptances
    ADD CONSTRAINT user_terms_acceptances_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: verification_tokens verification_tokens_token_key; Type: CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.verification_tokens
    ADD CONSTRAINT verification_tokens_token_key UNIQUE (token);


--
-- Name: idx_accounts_userid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_accounts_userid ON public.accounts USING btree ("userId");


--
-- Name: idx_answers_assessmentid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_answers_assessmentid ON public.answers USING btree ("assessmentId");


--
-- Name: idx_assessments_completed; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_assessments_completed ON public.assessments USING btree ("completedAt");


--
-- Name: idx_assessments_created; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_assessments_created ON public.assessments USING btree ("createdAt");


--
-- Name: idx_assessments_isostandardid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_assessments_isostandardid ON public.assessments USING btree ("isoStandardId");


--
-- Name: idx_assessments_status; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_assessments_status ON public.assessments USING btree (status);


--
-- Name: idx_assessments_userid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_assessments_userid ON public.assessments USING btree ("userId");


--
-- Name: idx_categories_active; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_categories_active ON public.categories USING btree (active);


--
-- Name: idx_categories_parent; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_categories_parent ON public.categories USING btree ("parentId");


--
-- Name: idx_categories_parentid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_categories_parentid ON public.categories USING btree (parentid);


--
-- Name: idx_categories_slug; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_categories_slug ON public.categories USING btree (slug);


--
-- Name: idx_clauses_isostandard_order; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_clauses_isostandard_order ON public.clauses USING btree ("isoStandardId", "order");


--
-- Name: idx_iso_standards_active; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_iso_standards_active ON public.iso_standards USING btree (active);


--
-- Name: idx_iso_standards_code; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_iso_standards_code ON public.iso_standards USING btree (code);


--
-- Name: idx_leads_assignedpartnerid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_leads_assignedpartnerid ON public.leads USING btree ("assignedPartnerId");


--
-- Name: idx_leads_created; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_leads_created ON public.leads USING btree ("createdAt");


--
-- Name: idx_leads_isostandardid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_leads_isostandardid ON public.leads USING btree ("isoStandardId");


--
-- Name: idx_leads_status; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_leads_status ON public.leads USING btree (status);


--
-- Name: idx_leads_userid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_leads_userid ON public.leads USING btree ("userId");


--
-- Name: idx_messages_createdat; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_messages_createdat ON public.messages USING btree ("createdAt");


--
-- Name: idx_messages_leadid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_messages_leadid ON public.messages USING btree ("leadId");


--
-- Name: idx_messages_senderid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_messages_senderid ON public.messages USING btree ("senderId");


--
-- Name: idx_nominations_email; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_nominations_email ON public.nominations USING btree ("nominatorEmail");


--
-- Name: idx_nominations_status; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_nominations_status ON public.nominations USING btree (status);


--
-- Name: idx_order_items_orderid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_order_items_orderid ON public.order_items USING btree ("orderId");


--
-- Name: idx_orders_created; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_orders_created ON public.orders USING btree ("createdAt");


--
-- Name: idx_orders_status; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_orders_status ON public.orders USING btree (status);


--
-- Name: idx_orders_userid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_orders_userid ON public.orders USING btree ("userId");


--
-- Name: idx_pending_orders_checkout_request; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_orders_checkout_request ON public.pending_orders USING btree (checkoutrequestid);


--
-- Name: idx_pending_orders_expires; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_orders_expires ON public.pending_orders USING btree (expiresat);


--
-- Name: idx_pending_orders_status; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_orders_status ON public.pending_orders USING btree (status);


--
-- Name: idx_pending_orders_user; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_pending_orders_user ON public.pending_orders USING btree (userid);


--
-- Name: idx_product_category_recommendations_category; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_category_recommendations_category ON public.product_category_recommendations USING btree (category_id);


--
-- Name: idx_product_category_recommendations_product; Type: INDEX; Schema: public; Owner: postgres
--

CREATE INDEX idx_product_category_recommendations_product ON public.product_category_recommendations USING btree (product_id);


--
-- Name: idx_products_active; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_products_active ON public.products USING btree (active);


--
-- Name: idx_products_category; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_products_category ON public.products USING btree ("categoryId");


--
-- Name: idx_products_created; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_products_created ON public.products USING btree ("createdAt");


--
-- Name: idx_products_sku; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_products_sku ON public.products USING btree (sku);


--
-- Name: idx_questions_clause_order; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_questions_clause_order ON public.questions USING btree ("clauseId", "order");


--
-- Name: idx_sessions_sessiontoken; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_sessions_sessiontoken ON public.sessions USING btree ("sessionToken");


--
-- Name: idx_sessions_userid; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_sessions_userid ON public.sessions USING btree ("userId");


--
-- Name: idx_site_settings_key; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_site_settings_key ON public.site_settings USING btree (key);


--
-- Name: idx_users_created; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_users_created ON public.users USING btree ("createdAt");


--
-- Name: idx_users_email; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_users_email ON public.users USING btree (email);


--
-- Name: idx_users_role; Type: INDEX; Schema: public; Owner: iso_app
--

CREATE INDEX idx_users_role ON public.users USING btree (role);


--
-- Name: answers update_answers_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_answers_updated_at BEFORE UPDATE ON public.answers FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: assessments update_assessments_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_assessments_updated_at BEFORE UPDATE ON public.assessments FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: categories update_categories_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_categories_updated_at BEFORE UPDATE ON public.categories FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: clauses update_clauses_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_clauses_updated_at BEFORE UPDATE ON public.clauses FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: iso_standards update_iso_standards_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_iso_standards_updated_at BEFORE UPDATE ON public.iso_standards FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: leads update_leads_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_leads_updated_at BEFORE UPDATE ON public.leads FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: nominations update_nominations_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_nominations_updated_at BEFORE UPDATE ON public.nominations FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: orders update_orders_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_orders_updated_at BEFORE UPDATE ON public.orders FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: products update_products_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_products_updated_at BEFORE UPDATE ON public.products FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: questions update_questions_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_questions_updated_at BEFORE UPDATE ON public.questions FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: site_settings update_site_settings_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_site_settings_updated_at BEFORE UPDATE ON public.site_settings FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: users update_users_updated_at; Type: TRIGGER; Schema: public; Owner: iso_app
--

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_updated_at_column();


--
-- Name: accounts accounts_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.accounts
    ADD CONSTRAINT accounts_userid_fkey FOREIGN KEY ("userId") REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: answers answers_assessmentid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_assessmentid_fkey FOREIGN KEY ("assessmentId") REFERENCES public.assessments(id) ON DELETE CASCADE;


--
-- Name: answers answers_questionid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.answers
    ADD CONSTRAINT answers_questionid_fkey FOREIGN KEY ("questionId") REFERENCES public.questions(id) ON DELETE CASCADE;


--
-- Name: assessments assessments_isostandardid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.assessments
    ADD CONSTRAINT assessments_isostandardid_fkey FOREIGN KEY ("isoStandardId") REFERENCES public.iso_standards(id);


--
-- Name: assessments assessments_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.assessments
    ADD CONSTRAINT assessments_userid_fkey FOREIGN KEY ("userId") REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: categories categories_parentId_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT "categories_parentId_fkey" FOREIGN KEY ("parentId") REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: clauses clauses_isostandardid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.clauses
    ADD CONSTRAINT clauses_isostandardid_fkey FOREIGN KEY ("isoStandardId") REFERENCES public.iso_standards(id) ON DELETE CASCADE;


--
-- Name: products fk_products_category; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT fk_products_category FOREIGN KEY ("categoryId") REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: iso_settings iso_settings_standard_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.iso_settings
    ADD CONSTRAINT iso_settings_standard_id_fkey FOREIGN KEY (standard_id) REFERENCES public.standards(id);


--
-- Name: leads leads_isostandardid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_isostandardid_fkey FOREIGN KEY ("isoStandardId") REFERENCES public.iso_standards(id);


--
-- Name: leads leads_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.leads
    ADD CONSTRAINT leads_userid_fkey FOREIGN KEY ("userId") REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: messages messages_leadId_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT "messages_leadId_fkey" FOREIGN KEY ("leadId") REFERENCES public.leads(id) ON DELETE CASCADE;


--
-- Name: messages messages_senderId_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.messages
    ADD CONSTRAINT "messages_senderId_fkey" FOREIGN KEY ("senderId") REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_orderid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_orderid_fkey FOREIGN KEY ("orderId") REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_productid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_productid_fkey FOREIGN KEY ("productId") REFERENCES public.products(id);


--
-- Name: orders orders_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_userid_fkey FOREIGN KEY ("userId") REFERENCES public.users(id);


--
-- Name: pending_orders pending_orders_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.pending_orders
    ADD CONSTRAINT pending_orders_userid_fkey FOREIGN KEY (userid) REFERENCES public.users(id);


--
-- Name: product_images product_images_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_recommendations product_recommendations_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_recommendations
    ADD CONSTRAINT product_recommendations_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: product_recommendations product_recommendations_recommended_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.product_recommendations
    ADD CONSTRAINT product_recommendations_recommended_product_id_fkey FOREIGN KEY (recommended_product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: questions questions_clauseid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.questions
    ADD CONSTRAINT questions_clauseid_fkey FOREIGN KEY ("clauseId") REFERENCES public.clauses(id);


--
-- Name: sessions sessions_userid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: iso_app
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_userid_fkey FOREIGN KEY ("userId") REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: user_answers user_answers_assessment_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_answers
    ADD CONSTRAINT user_answers_assessment_id_fkey FOREIGN KEY (assessment_id) REFERENCES public.user_assessments(id);


--
-- Name: user_answers user_answers_question_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_answers
    ADD CONSTRAINT user_answers_question_id_fkey FOREIGN KEY (question_id) REFERENCES public.questions(id);


--
-- Name: user_assessments user_assessments_standard_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_assessments
    ADD CONSTRAINT user_assessments_standard_id_fkey FOREIGN KEY (standard_id) REFERENCES public.standards(id);


--
-- Name: user_terms_acceptances user_terms_acceptances_terms_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.user_terms_acceptances
    ADD CONSTRAINT user_terms_acceptances_terms_id_fkey FOREIGN KEY (terms_id) REFERENCES public.terms_and_conditions(id);


--
-- Name: SCHEMA public; Type: ACL; Schema: -; Owner: pg_database_owner
--

GRANT ALL ON SCHEMA public TO iso_app;


--
-- Name: TABLE about_us; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.about_us TO iso_app;


--
-- Name: SEQUENCE about_us_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.about_us_id_seq TO iso_app;


--
-- Name: TABLE iso_settings; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.iso_settings TO iso_app;


--
-- Name: SEQUENCE iso_settings_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.iso_settings_id_seq TO iso_app;


--
-- Name: TABLE partners; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.partners TO iso_app;


--
-- Name: SEQUENCE partners_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.partners_id_seq TO iso_app;


--
-- Name: TABLE pending_orders; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.pending_orders TO iso_app;


--
-- Name: TABLE product_category_recommendations; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.product_category_recommendations TO iso_app;


--
-- Name: SEQUENCE product_category_recommendations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_category_recommendations_id_seq TO iso_app;


--
-- Name: TABLE product_images; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.product_images TO iso_app;


--
-- Name: SEQUENCE product_images_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_images_id_seq TO iso_app;


--
-- Name: TABLE product_recommendations; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.product_recommendations TO iso_app;


--
-- Name: SEQUENCE product_recommendations_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.product_recommendations_id_seq TO iso_app;


--
-- Name: TABLE standards; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.standards TO iso_app;


--
-- Name: SEQUENCE standards_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.standards_id_seq TO iso_app;


--
-- Name: TABLE terms_and_conditions; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.terms_and_conditions TO iso_app;


--
-- Name: SEQUENCE terms_and_conditions_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.terms_and_conditions_id_seq TO iso_app;


--
-- Name: TABLE user_answers; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.user_answers TO iso_app;


--
-- Name: SEQUENCE user_answers_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.user_answers_id_seq TO iso_app;


--
-- Name: TABLE user_assessments; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.user_assessments TO iso_app;


--
-- Name: SEQUENCE user_assessments_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.user_assessments_id_seq TO iso_app;


--
-- Name: TABLE user_terms_acceptances; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON TABLE public.user_terms_acceptances TO iso_app;


--
-- Name: SEQUENCE user_terms_acceptances_id_seq; Type: ACL; Schema: public; Owner: postgres
--

GRANT ALL ON SEQUENCE public.user_terms_acceptances_id_seq TO iso_app;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON SEQUENCES TO iso_app;


--
-- Name: DEFAULT PRIVILEGES FOR FUNCTIONS; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON FUNCTIONS TO iso_app;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: public; Owner: postgres
--

ALTER DEFAULT PRIVILEGES FOR ROLE postgres IN SCHEMA public GRANT ALL ON TABLES TO iso_app;


--
-- PostgreSQL database dump complete
--

\unrestrict CRoC50fx5DdldYKhvzGdbyIHsXXYUS9FhA8P91Hdffc40mStwk7pWArR1vBT5oh

