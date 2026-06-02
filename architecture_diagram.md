# Money Tracker System - Architecture Diagrams
## Laravel + Bootstrap Local-First Development

## 1. High-Level System Architecture (Laravel Monolith)

```mermaid
graph TB
    subgraph Users
        U1[Family Member 1]
        U2[Family Member 2]
        U3[Family Member N]
    end
    
    subgraph Telegram
        TB[Telegram Bot API]
    end
    
    subgraph Laravel Application
        ROUTES[Routes<br/>web.php & api.php]
        CTRL[Controllers<br/>Dashboard, Transaction, Telegram]
        MODELS[Eloquent Models<br/>Family, User, Transaction]
        VIEWS[Blade Views<br/>Bootstrap 5 UI]
    end
    
    subgraph Local Development
        SERVER[PHP Built-in Server<br/>or Laragon/XAMPP]
        DB[(MySQL Database<br/>Local)]
    end
    
    U1 -->|Messages| TB
    U2 -->|Messages| TB
    U3 -->|Messages| TB
    TB -->|Webhook/Polling| ROUTES
    
    U1 -->|HTTP| SERVER
    U2 -->|HTTP| SERVER
    U3 -->|HTTP| SERVER
    
    SERVER -->|Request| ROUTES
    ROUTES -->|Web Routes| CTRL
    ROUTES -->|API Routes| CTRL
    
    CTRL -->|Query| MODELS
    CTRL -->|Render| VIEWS
    
    MODELS -->|Eloquent ORM| DB
    
    VIEWS -->|HTML+Bootstrap| SERVER
```

## 2. Data Flow - Transaction Creation via Telegram (Laravel)

```mermaid
sequenceDiagram
    participant User
    participant Telegram
    participant Laravel
    participant Controller
    participant Model
    participant DB
    
    User->>Telegram: /expense 50000 Food Lunch
    Telegram->>Laravel: POST /api/telegram/webhook
    Laravel->>Controller: TelegramController@handleWebhook
    Controller->>Controller: Parse command
    Controller->>Controller: Validate format
    
    alt Valid command
        Controller->>Model: User::findByTelegramId
        Model->>DB: SELECT user
        DB-->>Model: User data
        Model-->>Controller: User object
        Controller->>Model: Transaction::create
        Model->>DB: INSERT transaction
        DB-->>Model: Success
        Model-->>Controller: Transaction object
        Controller->>Telegram: sendMessage API
        Telegram->>User: ✅ Expense logged: Rp 50,000
    else Invalid command
        Controller->>Telegram: sendMessage API
        Telegram->>User: ❌ Invalid format
    end
```

## 3. Data Flow - Web Dashboard Access (Laravel + Blade)

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant Laravel
    participant Controller
    participant Model
    participant DB
    participant Blade
    
    User->>Browser: GET /dashboard
    Browser->>Laravel: HTTP Request
    Laravel->>Laravel: Check session auth
    
    alt Authenticated
        Laravel->>Controller: DashboardController@index
        Controller->>Model: Transaction::with relations
        Model->>DB: SELECT with joins
        DB-->>Model: Transaction data
        Model-->>Controller: Collection
        Controller->>Controller: Calculate summaries
        Controller->>Blade: Pass data to view
        Blade->>Blade: Render HTML + Bootstrap
        Blade-->>Laravel: Compiled HTML
        Laravel-->>Browser: HTML Response
        Browser->>Browser: Render page + Chart.js
        Browser->>User: Display dashboard
    else Not authenticated
        Laravel->>Browser: Redirect to /login
        Browser->>User: Show login page
    end
```

## 4. Database Entity Relationship Diagram

```mermaid
erDiagram
    FAMILIES ||--o{ USERS : has
    FAMILIES ||--o{ TRANSACTIONS : owns
    FAMILIES ||--o{ CATEGORIES : defines
    USERS ||--o{ TRANSACTIONS : creates
    CATEGORIES ||--o{ TRANSACTIONS : categorizes
    
    FAMILIES {
        int id PK
        varchar name
        varchar invite_code UK
        timestamp created_at
        timestamp updated_at
    }
    
    USERS {
        int id PK
        int family_id FK
        bigint telegram_id UK
        varchar name
        enum role
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }
    
    CATEGORIES {
        int id PK
        int family_id FK
        varchar name
        enum type
        boolean is_active
        timestamp created_at
    }
    
    TRANSACTIONS {
        int id PK
        int family_id FK
        int user_id FK
        int category_id FK
        enum type
        decimal amount
        varchar description
        date transaction_date
        timestamp created_at
        timestamp updated_at
        boolean is_deleted
    }
```

## 5. Laravel Application Structure

```mermaid
graph TB
    subgraph Routes
        WEB[web.php<br/>Web Routes]
        API[api.php<br/>API Routes]
    end
    
    subgraph Middleware
        AUTH[Authenticate]
        CSRF[VerifyCsrfToken]
        FAMILY[CheckFamilyAccess]
    end
    
    subgraph Controllers
        DASH[DashboardController]
        TRANS[TransactionController]
        TELE[TelegramController]
        CAT[CategoryController]
    end
    
    subgraph Models
        FAM[Family Model]
        USER[User Model]
        TRAN[Transaction Model]
        CATE[Category Model]
    end
    
    subgraph Views
        LAYOUT[layouts/app.blade.php]
        DASHV[dashboard.blade.php]
        TRANSV[transactions/index.blade.php]
        REPV[reports.blade.php]
    end
    
    subgraph Database
        DB[(MySQL)]
    end
    
    WEB --> MIDDLEWARE
    API --> MIDDLEWARE
    MIDDLEWARE --> Controllers
    
    DASH --> Models
    TRANS --> Models
    TELE --> Models
    CAT --> Models
    
    Models --> DB
    
    DASH --> Views
    TRANS --> Views
    
    Views --> LAYOUT
```

## 6. Local Development Environment

```mermaid
graph TB
    subgraph Development Machine
        subgraph Laravel Application
            APP[Laravel App<br/>Port 8000]
            ENV[.env Config]
            ARTISAN[Artisan CLI]
        end
        
        subgraph Local Server Options
            BUILT[PHP Built-in Server<br/>php artisan serve]
            LARA[Laragon<br/>Windows]
            XAMPP[XAMPP<br/>Cross-platform]
            VALET[Laravel Valet<br/>macOS]
        end
        
        subgraph Database
            MYSQL[(MySQL 8.0<br/>Port 3306)]
            ADMIN[phpMyAdmin<br/>Database GUI]
        end
        
        subgraph Development Tools
            COMPOSER[Composer<br/>Dependencies]
            NPM[NPM<br/>Frontend Assets]
            GIT[Git<br/>Version Control]
        end
        
        subgraph Testing Tools
            NGROK[ngrok<br/>Telegram Webhook]
            TINKER[Laravel Tinker<br/>REPL]
        end
    end
    
    BUILT -.->|Option 1| APP
    LARA -.->|Option 2| APP
    XAMPP -.->|Option 3| APP
    VALET -.->|Option 4| APP
    
    APP --> MYSQL
    APP --> ENV
    ARTISAN --> APP
    
    COMPOSER --> APP
    NPM --> APP
    GIT --> APP
    
    NGROK -.->|Tunnel| APP
    TINKER --> APP
    ADMIN --> MYSQL
```

## 7. User Authentication Flow

```mermaid
flowchart TD
    START([User Opens Telegram Bot])
    START --> CHECK{First Time User?}
    
    CHECK -->|Yes| INVITE[Enter Invite Code]
    CHECK -->|No| AUTH[Authenticate via Telegram ID]
    
    INVITE --> VALIDATE{Valid Code?}
    VALIDATE -->|No| ERROR[Show Error]
    ERROR --> INVITE
    VALIDATE -->|Yes| REGISTER[Register User]
    
    REGISTER --> LINK[Link Telegram ID to Family]
    LINK --> SUCCESS[Registration Complete]
    
    AUTH --> VERIFY{User Exists?}
    VERIFY -->|No| INVITE
    VERIFY -->|Yes| ACTIVE{Account Active?}
    
    ACTIVE -->|No| INACTIVE[Account Inactive]
    ACTIVE -->|Yes| SUCCESS
    
    SUCCESS --> READY([Ready to Use Bot])
```

## 8. Transaction Processing Flow

```mermaid
flowchart TD
    START([Receive Bot Command])
    START --> PARSE[Parse Command]
    PARSE --> VALIDATE{Valid Format?}
    
    VALIDATE -->|No| ERROR1[Return Format Error]
    VALIDATE -->|Yes| CHECKUSER{User Authorized?}
    
    CHECKUSER -->|No| ERROR2[Return Auth Error]
    CHECKUSER -->|Yes| CHECKCAT{Category Exists?}
    
    CHECKCAT -->|No| ERROR3[Return Category Error]
    CHECKCAT -->|Yes| AMOUNT{Valid Amount?}
    
    AMOUNT -->|No| ERROR4[Return Amount Error]
    AMOUNT -->|Yes| SAVE[Save to Database]
    
    SAVE --> CALC[Calculate New Balance]
    CALC --> NOTIFY[Send Confirmation]
    NOTIFY --> UPDATE[Update Dashboard]
    UPDATE --> END([Transaction Complete])
    
    ERROR1 --> END
    ERROR2 --> END
    ERROR3 --> END
    ERROR4 --> END
```

## 9. Monthly Summary Generation

```mermaid
flowchart TD
    START([Request Monthly Summary])
    START --> PARAMS[Get Month/Year Parameters]
    PARAMS --> QUERY[Query Transactions]
    
    QUERY --> FILTER[Filter by Date Range]
    FILTER --> SPLIT{Split by Type}
    
    SPLIT -->|Income| INCOME[Sum Income]
    SPLIT -->|Expense| EXPENSE[Sum Expenses]
    
    INCOME --> CALC[Calculate Balance]
    EXPENSE --> CALC
    
    CALC --> CATEGORY[Group by Category]
    CATEGORY --> PERCENT[Calculate Percentages]
    PERCENT --> SORT[Sort by Amount]
    SORT --> FORMAT[Format Response]
    FORMAT --> CACHE[Cache Result]
    CACHE --> RETURN([Return Summary])
```

## 10. Security Layers

```mermaid
graph TB
    subgraph External
        USER[User]
        ATTACKER[Potential Attacker]
    end
    
    subgraph Security Layer 1
        HTTPS[HTTPS/TLS Encryption]
        RATE[Rate Limiting]
    end
    
    subgraph Security Layer 2
        AUTH[Authentication]
        JWT[JWT Validation]
    end
    
    subgraph Security Layer 3
        AUTHZ[Authorization]
        FAMILY[Family Isolation]
    end
    
    subgraph Security Layer 4
        INPUT[Input Validation]
        SANITIZE[Data Sanitization]
    end
    
    subgraph Security Layer 5
        SQL[Parameterized Queries]
        ENCRYPT[Data Encryption]
    end
    
    USER --> HTTPS
    ATTACKER -.->|Blocked| RATE
    
    HTTPS --> RATE
    RATE --> AUTH
    AUTH --> JWT
    JWT --> AUTHZ
    AUTHZ --> FAMILY
    FAMILY --> INPUT
    INPUT --> SANITIZE
    SANITIZE --> SQL
    SQL --> ENCRYPT
    
    ENCRYPT --> DB[(Secure Database)]
```

## 11. API Request Flow

```mermaid
sequenceDiagram
    participant Client
    participant Nginx
    participant API
    participant Auth
    participant Service
    participant DB
    
    Client->>Nginx: HTTPS Request
    Nginx->>Nginx: SSL Termination
    Nginx->>API: Forward Request
    API->>API: Rate Limit Check
    API->>Auth: Validate Token
    Auth-->>API: Token Valid
    API->>Service: Process Request
    Service->>DB: Query Data
    DB-->>Service: Return Data
    Service->>Service: Business Logic
    Service-->>API: Response Data
    API->>API: Format Response
    API-->>Nginx: JSON Response
    Nginx-->>Client: HTTPS Response
```

## 12. Backup and Recovery Strategy

```mermaid
flowchart LR
    subgraph Production
        PROD[(Production DB)]
    end
    
    subgraph Backup Process
        DAILY[Daily Full Backup<br/>2 AM UTC]
        HOURLY[Hourly Incremental<br/>Every Hour]
        BINLOG[Binary Log Backup<br/>Real-time]
    end
    
    subgraph Storage
        S3[Cloud Storage<br/>S3/GCS]
        LOCAL[Local Backup<br/>7 Days]
    end
    
    subgraph Recovery
        RESTORE[Restore Process]
        VERIFY[Verify Integrity]
        SWITCH[Switch to Backup]
    end
    
    PROD -->|Full| DAILY
    PROD -->|Incremental| HOURLY
    PROD -->|Continuous| BINLOG
    
    DAILY --> S3
    HOURLY --> LOCAL
    BINLOG --> S3
    
    S3 -.->|Disaster| RESTORE
    LOCAL -.->|Quick| RESTORE
    
    RESTORE --> VERIFY
    VERIFY --> SWITCH
    SWITCH -.-> PROD
```

---

## Architecture Notes

### Scalability Considerations
- **Horizontal Scaling**: Add more application servers behind load balancer
- **Database Scaling**: Implement read replicas for query distribution
- **Caching**: Add Redis for frequently accessed data
- **CDN**: Use CDN for static web assets

### High Availability
- **Multi-AZ Deployment**: Deploy across multiple availability zones
- **Database Replication**: Primary-replica setup with automatic failover
- **Health Checks**: Continuous monitoring with automatic recovery
- **Backup Strategy**: Multiple backup tiers with tested recovery procedures

### Performance Optimization
- **Database Indexing**: Strategic indexes on frequently queried columns
- **Query Optimization**: Efficient SQL queries with proper joins
- **API Caching**: Cache frequently requested summaries
- **Connection Pooling**: Reuse database connections

### Monitoring and Observability
- **Application Logs**: Structured logging with log aggregation
- **Metrics**: Track response times, error rates, throughput
- **Alerts**: Automated alerts for critical issues
- **Dashboards**: Real-time system health dashboards