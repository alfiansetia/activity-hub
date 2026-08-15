# Activity Hub - Application Activity Diagrams

## Overview

**Activity Hub** is a Laravel-based activity management system with three roles:

| Role                 | Description                                                        |
| -------------------- | ------------------------------------------------------------------ |
| **Admin**            | Manages users, companies, and can delete any activity              |
| **Dosen** (Lecturer) | Reviews and approves/rejects activities, browses by company → user |
| **User** (Student)   | Creates activities, requests to join a company                     |

### Entity Relationship

```mermaid
erDiagram
    Company ||--o{ User : has
    Company ||--o{ Activity : contains
    User ||--o{ Activity : creates
    User }o--o| User : acceptor
    User }o--o| User : rejector
    Activity ||--o{ Attachment : has

    Company {
        int id PK
        string name
    }
    User {
        int id PK
        string name
        string email
        string role
        int company_id FK
        string company_status
    }
    Activity {
        int id PK
        string title
        timestamp date
        string status
        int user_id FK
        int company_id FK
        int accept_by FK
        int reject_by FK
        text reject_reason
    }
    Attachment {
        int id PK
        int activity_id FK
        string image_url
        string caption
    }
```

---

## 1. Authentication Flow

```mermaid
flowchart TD
    Start([User visits site]) --> Landing[Welcome Landing Page]
    Landing --> Choice{Choose action}
    Choice -->|Login| Login[GET /login]
    Choice -->|Register| Register[GET /register]

    Login --> LoginFill[Fill email and password]
    LoginFill --> LoginSubmit[POST /login]
    LoginSubmit --> LoginValid{Credentials valid?}
    LoginValid -->|Yes| Dashboard[Redirect to /dashboard]
    LoginValid -->|No| LoginError[Show error message]
    LoginError --> Login

    Register --> RegFill[Fill name email password]
    RegFill --> RegSubmit[POST /register]
    RegSubmit --> RegValid{Validation passed?}
    RegValid -->|Yes| RegCreate[Create User with role=user  company_status=none]
    RegCreate --> RegLogin[Auto login]
    RegLogin --> Dashboard
    RegValid -->|No| RegError[Show validation errors]
    RegError --> Register

    Dashboard --> Logout[POST /logout]
    Logout --> Landing
```

---

## 2. Company Join Request Flow

When a new user registers, their `company_status` is `none`. They must request to join a company before they can create activities.

```mermaid
flowchart TD
    Start([User with company_status=none]) --> Profile[Visit /profile]
    Profile --> SelectCompany[Select a company from dropdown]
    SelectCompany --> SubmitRequest[POST /profile/company-request]
    SubmitRequest --> CheckSame{Same as current company?}
    CheckSame -->|Yes| ErrorSame[Show error: already member]
    ErrorSame --> Profile
    CheckSame -->|No| SetPending[Set company_status=pending  company_id=selected]
    SetPending --> WaitApproval[Wait for admin approval]

    WaitApproval --> AdminReview[Admin visits /admin/users]
    AdminReview --> AdminAction{Admin decision}
    AdminAction -->|Approve| Approve[POST /admin/users/user/approve]
    Approve --> SetAccept[Set company_status=accept]
    SetAccept --> CanCreate[User can now create activities]
    AdminAction -->|Reject| Reject[POST /admin/users/user/reject]
    Reject --> SetReject[Set company_status=reject  with reason]
    SetReject --> UserRetry[User sees rejection reason on profile]
    UserRetry --> Profile
```

---

## 3. Activity Lifecycle (Core Flow)

This is the central workflow of the application. Activities go through three statuses: `pending` → `accept` or `reject`.

```mermaid
flowchart TD
    Start([User with approved company]) --> Create[GET /activities/create]
    Create --> FillForm[Fill activity form]
    FillForm --> Fields[Title, descriptions, rules, tools, location, tests, ulasan, images]
    Fields --> Submit[POST /activities]
    Submit --> Validate{Validation passed?}
    Validate -->|No| FormError[Show validation errors]
    FormError --> Create
    Validate -->|Yes| SaveActivity[Create Activity with status=pending]
    SaveActivity --> SaveImages[Save base64 images as attachments]
    SaveImages --> Index[Redirect to activities list]

    Index --> DosenReview[Dosen reviews activity]
    DosenReview --> Decision{Dosen decision}
    Decision -->|Accept| AcceptBtn[POST /activities/activity/accept]
    AcceptBtn --> AcceptUpdate[Set status=accept  accept_by=dosen  accept_at=now]
    AcceptUpdate --> Done([Activity accepted - cannot be edited])
    Decision -->|Reject| RejectBtn[POST /activities/activity/reject]
    RejectBtn --> RejectUpdate[Set status=reject  reject_by=dosen  reject_reason=provided]
    RejectUpdate --> CanEdit[User can edit and resubmit]

    CanEdit --> EditActivity[GET /activities/activity/edit]
    EditActivity --> EditForm[Update title, descriptions, images etc]
    EditForm --> UpdateSubmit[PUT /activities/activity]
    UpdateSubmit --> Resubmit[Reset status=pending  clear reject fields]
    Resubmit --> SetResubmit[Set re_submit_at=now]
    SetResubmit --> DosenReview
```

---

## 4. Activity Deletion Flow

```mermaid
flowchart TD
    Start([Delete activity]) --> WhoCheck{Who is deleting?}
    WhoCheck -->|Admin| AdminDelete[Admin can delete any activity in any status]
    AdminDelete --> DoDelete[DELETE /activities/activity]
    WhoCheck -->|User Owner| OwnerCheck{Activity status?}
    OwnerCheck -->|Pending| OwnerDelete[Owner can delete their pending activity]
    OwnerDelete --> DoDelete
    OwnerCheck -->|Accept or Reject| Forbidden[403 Forbidden]
    WhoCheck -->|Other User| Forbidden
    DoDelete --> CleanFiles[Delete all attachment files from storage]
    CleanFiles --> Deleted[Activity deleted]
```

---

## 5. Dosen Browsing Flow

Dosen has a unique hierarchical browsing experience: Company → User → Activities

```mermaid
flowchart TD
    Start([Dosen opens /activities]) --> CompanyList[View all companies with activity counts]
    CompanyList --> CompanyCounts[Shows: total, pending, accept, reject per company]
    CompanyCounts --> SelectCompany[Click a company]
    SelectCompany --> UserList[View users in that company with activity counts]
    UserList --> SelectUser[Click a user]
    SelectUser --> ActivityList[View that user's activities with filters]
    ActivityList --> Filter{Filter by status?}
    Filter -->|All| ShowAll[Show all activities]
    Filter -->|Pending| ShowPending[Show pending only]
    Filter -->|Accept| ShowAccept[Show accepted only]
    Filter -->|Reject| ShowReject[Show rejected only]
    Filter -->|Search| SearchTitle[Search by title]
```

---

## 6. Activity Status State Machine

```mermaid
stateDiagram-v2
    [*] --> Pending: User creates activity
    Pending --> Accept: Dosen accepts
    Pending --> Reject: Dosen rejects
    Pending --> [*]: User/Admin deletes

    Reject --> Pending: User edits and resubmits

    Accept --> [*]: Cannot be edited or resubmitted
    Accept --> [*]: Admin can delete

    Reject --> [*]: User/Admin can delete
```

---

## 7. Admin Management Flows

### 7a. User Management

```mermaid
flowchart TD
    Start([Admin opens /admin/users]) --> UserList[View all users with filters]
    UserList --> Actions{Choose action}
    Actions -->|Create| CreateForm[GET /admin/users/create]
    CreateForm --> CreateSubmit[POST /admin/users]
    CreateSubmit --> CreateFields[name email password role company company_status]
    CreateFields --> Created[User created]

    Actions -->|Approve| ApproveUser[POST /admin/users/user/approve]
    ApproveUser --> Approved[Set company_status=accept]

    Actions -->|Reject| RejectUser[POST /admin/users/user/reject]
    RejectUser --> Rejected[Set company_status=reject with reason]

    Actions -->|Change Role| UpdateRole[PATCH /admin/users/user/role]
    UpdateRole --> RoleChanged[Role changed to admin dosen or user]

    Actions -->|Edit| EditUser[PATCH /admin/users/user]
    EditUser --> EditFields[Update role company company_status]
    EditFields --> Updated[User updated]

    Actions -->|Delete| DeleteUser[DELETE /admin/users/user]
    DeleteUser --> SelfCheck{Deleting self?}
    SelfCheck -->|Yes| Error[Show error: cannot delete yourself]
    SelfCheck -->|No| Deleted[User deleted]
```

### 7b. Company Management

```mermaid
flowchart TD
    Start([Admin opens /admin/companies]) --> CompList[View all companies with activity counts]
    CompList --> Actions{Choose action}
    Actions -->|Create| CreateComp[POST /admin/companies]
    CreateComp --> CompCreated[Company created]

    Actions -->|Edit| EditComp[PUT /admin/companies/company]
    EditComp --> CompUpdated[Company name updated]

    Actions -->|Delete| DeleteComp[DELETE /admin/companies/company]
    DeleteComp --> HasActivities{Has activities?}
    HasActivities -->|Yes| Error[Show error: cannot delete]
    HasActivities -->|No| CompDeleted[Company deleted]
```

---

## 8. Dashboard Flow

```mermaid
flowchart TD
    Start([User opens /dashboard]) --> RoleCheck{User role?}
    RoleCheck -->|Admin or Dosen| AllStats[Show stats for ALL activities]
    RoleCheck -->|User| CompanyStats[Show stats for own company only]
    AllStats --> ShowStats[Display counters]
    CompanyStats --> ShowStats
    ShowStats --> Stats[Total, Pending, Accepted, Rejected]
    Stats --> Recent[Show 10 most recent activities]
    Recent --> CompanyList[Show all companies]
```

---

## 9. Calendar Flow

```mermaid
flowchart TD
    Start([User opens /calendar]) --> Validate{Valid month and year?}
    Validate -->|No| Redirect[Redirect with defaults]
    Validate -->|Yes| Clamp{Current year and future month?}
    Clamp -->|Yes| SetCurrent[Clamp to current month]
    Clamp -->|No| UseProvided[Use provided month and year]
    SetCurrent --> QueryDB
    UseProvided --> QueryDB

    QueryDB --> RoleFilter{User role?}
    RoleFilter -->|User| OwnOnly[Filter by own user_id]
    RoleFilter -->|Admin or Dosen| AllActivities[See all activities]
    OwnOnly --> GetData
    AllActivities --> GetData

    GetData --> Counts[Get activity counts by date]
    Counts --> StatusCounts[Group by: total, pending, accept, reject]
    StatusCounts --> Display[Render calendar with activity indicators]
    Display --> ClickDay[Click on a day to see activity details]
```

---

## 10. PDF Report Flow

```mermaid
flowchart TD
    Start([View activity detail]) --> Actions{Choose action}
    Actions -->|Preview| Preview[GET /activities/activity/preview]
    Actions -->|Download| Download[GET /activities/activity/pdf]
    Preview --> CheckAccess{Access check}
    Download --> CheckAccess
    CheckAccess -->|User| SameCompany{Same company?}
    SameCompany -->|No| Forbidden[403 Forbidden]
    SameCompany -->|Yes| GeneratePDF
    CheckAccess -->|Admin or Dosen| GeneratePDF[Prepare report data]
    GeneratePDF --> DayName[Calculate Malay day name]
    DayName --> RenderPDF[Render activities.pdf Blade template]
    RenderPDF --> PreviewAction[Preview: show in browser]
    RenderPDF --> DownloadAction[Download: save as PDF file]
```

---

## 11. Profile Management Flow

```mermaid
flowchart TD
    Start([User opens /profile]) --> ProfilePage[View profile page]
    ProfilePage --> Actions{Choose action}
    Actions -->|Update Name| UpdateName[PATCH /profile]
    UpdateName --> NameUpdated[Name updated]

    Actions -->|Change Password| ChangePw[PUT /profile/password]
    ChangePw --> PwCheck{Current password correct?}
    PwCheck -->|No| PwError[Show error]
    PwCheck -->|Yes| PwChanged[Password changed]

    Actions -->|Request Company| RequestCompany[POST /profile/company-request]
    RequestCompany --> CompanyFlow[See Company Join Request Flow above]
```

---

## 12. Complete System Overview

```mermaid
flowchart TB
    subgraph Guest[Unauthenticated]
        Landing[Welcome Page]
        Login[Login]
        Register[Register]
    end

    subgraph UserZone[User Role]
        UDashboard[Dashboard]
        UActivities[My Activities CRUD]
        UCalendar[Calendar View]
        UProfile[Profile and Company Request]
        UPDF[PDF Reports]
    end

    subgraph DosenZone[Dosen Role]
        DDashboard[Dashboard - All Data]
        DBrowse[Browse Company to User to Activities]
        DReview[Accept or Reject Activities]
        DCalendar[Calendar - All Data]
        DPDF[PDF Reports]
    end

    subgraph AdminZone[Admin Role]
        ADashboard[Dashboard - All Data]
        AUserMgmt[User Management CRUD]
        ACompanyMgmt[Company Management CRUD]
        AActivities[Delete Any Activity]
    end

    Landing --> Login
    Landing --> Register
    Register --> UDashboard
    Login --> UDashboard
    Login --> DDashboard
    Login --> ADashboard

    UDashboard --> UActivities
    UDashboard --> UCalendar
    UDashboard --> UProfile
    UActivities --> UPDF

    DDashboard --> DBrowse
    DDashboard --> DCalendar
    DBrowse --> DReview

    ADashboard --> AUserMgmt
    ADashboard --> ACompanyMgmt
```
