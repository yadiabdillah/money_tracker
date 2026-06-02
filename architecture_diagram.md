# Money Tracker System - Architecture Diagrams

## 1. High-Level System Architecture

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
    
    subgraph Application Layer
        BOT[Telegram Bot Service<br/>Node.js]
        API[Backend API<br/>Express.js]
        WEB[Web Frontend<br/>React/Vue]
    end
    
    subgraph Data Layer
        DB[(MySQL Database)]
        CACHE[Redis Cache<br/>Optional]
    end
    
    subgraph Infrastructure
        NGINX[Nginx Reverse Proxy]
        SSL[SSL/TLS Certificate]
    end
    
    U1 -->|Messages| TB
    U2 -->|Messages| TB
    U3 -->|Messages| TB
    TB -->|Webhook| BOT
    
    U1 -->|HTTPS| NGINX
    U2 -->|HTTPS| NGINX
    U3 -->|HTTPS| NGINX
    
    NGINX -->|Route| WEB
    NGINX -->|Route| API
    
    BOT -->|REST API| API
    WEB -->|REST API| API
    
    API -->|SQL Queries| DB
    API -.->|Cache| CACHE
    
    SSL -.->|Secure| NGINX
```

## 2. Data Flow - Transaction Creation via Telegram

```mermaid
sequenceDiagram
    participant User
    participant Telegram
    participant Bot
    participant API
    participant DB
    
    User->>Telegram: /expense 50000 Food Lunch
    Telegram->>Bot: Webhook notification
    Bot->>Bot: Parse command
    Bot->>Bot: Validate format
    
    alt Valid command
        Bot->>API: POST /api/transactions
        API->>API: Authenticate user
        API->>API: Validate category
        API->>DB: INSERT transaction
        DB-->>API: Success
        API-->>Bot: Transaction created
        Bot->>Telegram: Success message
        Telegram->>User: ✅ Expense logged: Rp 50,000
    else Invalid command
        Bot->>Telegram: Error message
        Telegram->>User: ❌ Invalid format
    end
```

## 3. Data Flow - Web Dashboard Access

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant Frontend
    participant API
    participant DB
    
    User->>Browser: Open dashboard
    Browser->>Frontend: Load application
    Frontend->>Frontend: Check auth token
    
    alt Authenticated
        Frontend->>API: GET /api/summary/monthly
        API->>API: Verify JWT token
        API->>DB: Query transactions
        DB-->>API: Transaction data
        API->>API: Calculate summaries
        API-->>Frontend: JSON response
        Frontend->>Frontend: Render charts
        Frontend->>Browser: Display dashboard
        Browser->>User: Show data
    else Not authenticated
        Frontend->>Browser: Redirect to login
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

## 5. Component Architecture

```mermaid
graph LR
    subgraph Telegram Bot Service
        BH[Bot Handler]
        CP[Command Parser]
        VM[Validation Module]
        AC[API Client]
    end
    
    subgraph Backend API
        RT[Router]
        MW[Middleware]
        CT[Controllers]
        SV[Services]
        RP[Repositories]
    end
    
    subgraph Web Frontend
        CP2[Components]
        ST[State Management]
        API2[API Client]
        RT2[Router]
    end
    
    BH --> CP
    CP --> VM
    VM --> AC
    AC --> RT
    
    RT --> MW
    MW --> CT
    CT --> SV
    SV --> RP
    
    API2 --> RT
    CP2 --> ST
    ST --> API2
    RT2 --> CP2
```

## 6. Deployment Architecture

```mermaid
graph TB
    subgraph Cloud Provider
        subgraph Load Balancer
            LB[Load Balancer]
        end
        
        subgraph Application Servers
            APP1[App Server 1<br/>Bot + API]
            APP2[App Server 2<br/>Bot + API]
        end
        
        subgraph Web Servers
            WEB1[Web Server 1<br/>Static Files]
            WEB2[Web Server 2<br/>Static Files]
        end
        
        subgraph Database Cluster
            DBMASTER[(MySQL Primary)]
            DBREPLICA[(MySQL Replica)]
        end
        
        subgraph Storage
            BACKUP[Backup Storage]
            LOGS[Log Storage]
        end
    end
    
    LB --> APP1
    LB --> APP2
    LB --> WEB1
    LB --> WEB2
    
    APP1 --> DBMASTER
    APP2 --> DBMASTER
    DBMASTER -.->|Replication| DBREPLICA
    
    APP1 -.->|Logs| LOGS
    APP2 -.->|Logs| LOGS
    DBMASTER -.->|Backup| BACKUP
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