# Green Medical App — Flutter Developer Prompt

You are building a **world-class medical mobile application** called **"Green Medical"** (تطبيق Green الطبي) using Flutter. This document contains everything you need: the complete API reference, design system, architecture guide, and feature spec. Follow every instruction precisely.

---

## 🏗️ Architecture & Tech Stack (NON-NEGOTIABLE)

### Architecture: Clean Architecture + BLoC

```
lib/
├── core/
│   ├── constants/          # app_colors, app_text_styles, app_sizes, app_strings
│   ├── errors/             # failures.dart, exceptions.dart
│   ├── network/            # dio_client.dart, api_interceptor.dart, network_info.dart
│   ├── usecases/           # base_usecase.dart (abstract)
│   ├── utils/              # validators, formatters, extensions
│   └── widgets/            # shared reusable widgets
│
├── features/
│   ├── auth/
│   │   ├── data/
│   │   │   ├── datasources/   auth_remote_datasource.dart
│   │   │   ├── models/        user_model.dart, otp_model.dart
│   │   │   └── repositories/  auth_repository_impl.dart
│   │   ├── domain/
│   │   │   ├── entities/      user_entity.dart
│   │   │   ├── repositories/  auth_repository.dart (abstract)
│   │   │   └── usecases/      send_otp_usecase.dart, verify_otp_usecase.dart, logout_usecase.dart
│   │   └── presentation/
│   │       ├── bloc/          auth_bloc.dart, auth_event.dart, auth_state.dart
│   │       └── pages/         phone_page.dart, otp_page.dart, profile_setup_page.dart
│   │
│   ├── home/
│   ├── store/
│   ├── nursing/
│   ├── bathing/
│   ├── care/
│   ├── lab/
│   ├── xray/
│   ├── doctors/
│   ├── nutrition/
│   ├── transfer/
│   ├── articles/
│   ├── forum/
│   └── sihati/             # صحتي room system
│
└── main.dart
```

### Required Packages
```yaml
dependencies:
  flutter_bloc: ^8.x
  dio: ^5.x
  get_it: ^7.x           # dependency injection
  injectable: ^2.x
  go_router: ^13.x       # navigation
  shared_preferences: ^2.x
  flutter_secure_storage: ^9.x
  cached_network_image: ^3.x
  shimmer: ^3.x          # loading skeletons
  lottie: ^3.x           # animations
  intl: ^0.19.x
  equatable: ^2.x
  dartz: ^0.10.x         # Either type for error handling
  flutter_animate: ^4.x  # micro-animations
  google_fonts: ^6.x
  fl_chart: ^0.x         # for health charts in Sihati
  pin_code_fields: ^8.x  # OTP input
  image_picker: ^1.x     # report image uploads
  geolocator: ^11.x      # location for requests
```

### BLoC Pattern Rules
- Every BLoC emits `Loading`, `Success`, `Error` states
- Use `Equatable` on all States and Events
- Use `Either<Failure, T>` from `dartz` in Repository and UseCase layers
- Never call API directly from UI — always go through BLoC → UseCase → Repository → DataSource

### Dependency Injection
- Use `get_it` + `injectable`
- Register: repositories, usecases, blocs, dio client
- Single `injection_container.dart` file

---

## 🌐 API Configuration

### Base URL
```
https://your-domain.com/api/v1
```
> Replace with actual domain. During development: `http://10.0.2.2/api/v1` (Android emulator to localhost)

### Headers
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}   ← required for protected routes
```

### Standard Response Envelope
**Every single API response** follows this format:
```json
{
  "success": true,
  "message": "رسالة باللغة العربية",
  "data": { ... }
}
```
```json
{
  "success": false,
  "message": "رسالة الخطأ",
  "errors": { "field": ["validation error"] }
}
```

**Create a base `ApiResponse<T>` model:**
```dart
class ApiResponse<T> {
  final bool success;
  final String message;
  final T? data;

  const ApiResponse({required this.success, required this.message, this.data});

  factory ApiResponse.fromJson(Map<String, dynamic> json, T Function(dynamic) fromJsonT) {
    return ApiResponse(
      success: json['success'],
      message: json['message'] ?? '',
      data: json['data'] != null ? fromJsonT(json['data']) : null,
    );
  }
}
```

### Paginated Response
```json
{
  "success": true,
  "message": "",
  "data": {
    "data": [ ...items... ],
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 73
  }
}
```

### Error Handling (Dio Interceptor)
- `401` → Clear token → Navigate to login
- `403` → Show "غير مصرح" snackbar
- `404` → Show "لم يتم العثور" message
- `422` → Show validation errors from `errors` field
- `500` → Show "خطأ في الخادم" message
- No internet → Show offline widget

---

## 🔐 Authentication

### Flow
1. User enters phone number → `POST /auth/send-otp`
2. Backend creates user if new, sends OTP (returned in response for dev)
3. User enters 6-digit OTP → `POST /auth/verify-otp`
4. Save `token` in `flutter_secure_storage` (never SharedPreferences for tokens)
5. All subsequent requests include `Authorization: Bearer {token}`

---

### POST `/auth/send-otp`
**Request:**
```json
{ "phone": "0791234567" }
```
**Response:**
```json
{
  "success": true,
  "message": "تم إرسال رمز التحقق",
  "data": {
    "otp_sent": true,
    "otp": "482910",
    "is_new_user": false
  }
}
```
> `is_new_user: true` → after OTP show profile setup screen (name/gender/DOB)
> In production `otp` field is removed — SMS sends it

---

### POST `/auth/verify-otp`
**Request:**
```json
{ "phone": "0791234567", "otp": "482910" }
```
**Response:**
```json
{
  "success": true,
  "message": "تم تسجيل الدخول بنجاح",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
    "user": {
      "id": 12,
      "name": "أحمد محمد",
      "phone": "0791234567",
      "email": null,
      "role": "patient",
      "gender": "male",
      "date_of_birth": "1990-05-15",
      "patient_code": "PT-A3F9K821",
      "is_active": true,
      "fcm_token": null,
      "created_at": "2026-08-04T10:00:00.000000Z"
    }
  }
}
```
> Save `token` to secure storage. Save `user` locally. Navigate to home.

---

### POST `/auth/logout` 🔒
**Response:** `{ "success": true, "message": "تم تسجيل الخروج بنجاح", "data": null }`
> Delete token from secure storage. Navigate to login.

---

### GET `/auth/me` 🔒
Returns current user object (same structure as above).

---

### PUT `/auth/profile` 🔒
**Request:**
```json
{
  "name": "أحمد محمد",
  "email": "ahmed@example.com",
  "date_of_birth": "1990-05-15",
  "gender": "male"
}
```
All fields optional. Returns updated user.

---

### PUT `/auth/fcm-token` 🔒
**Request:** `{ "fcm_token": "fFGxyz123..." }`
> Call this on app start after login to keep push notifications working.

---

## 🏠 Banners

### GET `/banners?section=home`
`section` = `home` or `store`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "خصم 20% على جميع المنتجات",
      "image_url": "https://domain.com/storage/banners/1.jpg",
      "link": null,
      "section": "home",
      "sort_order": 1
    }
  ]
}
```
> Display as full-width horizontal PageView with dots indicator. Auto-scroll every 4s.

---

## 🛒 Store

### GET `/store/categories`
**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "مستلزمات طبية",
      "image_url": "https://domain.com/storage/categories/1.jpg",
      "parent_id": null
    },
    {
      "id": 2,
      "name": "أجهزة",
      "image_url": "...",
      "parent_id": 1
    }
  ]
}
```
> Group by `parent_id == null` for root categories. Children are sub-categories.

---

### GET `/store/products?search=كمامة&category_id=1&page=1`
All params optional. Returns paginated products.

**Response data item:**
```json
{
  "id": 5,
  "name": "كمامة طبية N95",
  "description": "كمامة ثلاثية الطبقات...",
  "price": 2.500,
  "sale_price": 1.800,
  "images": [
    "https://domain.com/storage/products/img1.jpg",
    "https://domain.com/storage/products/img2.jpg"
  ],
  "category_id": 1,
  "is_active": true,
  "sort_order": 3,
  "category": { "id": 1, "name": "مستلزمات طبية", "image_url": "..." }
}
```
> If `sale_price != null`, show it as current price with `price` crossed out.

---

### GET `/store/products/{id}`
Returns single product (same structure).

---

### GET `/store/categories/{id}/products?search=&page=1`
Products filtered by category. Same response.

---

## 🛍️ Cart

### GET `/cart` 🔒
**Response:**
```json
{
  "data": {
    "id": 3,
    "total": 12.500,
    "items_count": 4,
    "items": [
      {
        "id": 7,
        "product_id": 5,
        "quantity": 2,
        "unit_price": 1.800,
        "subtotal": 3.600,
        "product": { "id": 5, "name": "كمامة طبية N95", "images": [...], "price": 2.500, "sale_price": 1.800 }
      }
    ]
  }
}
```

---

### POST `/cart/items` 🔒
**Request:** `{ "product_id": 5, "quantity": 2 }`
> If product already in cart → increments quantity. Returns full cart.

---

### PATCH `/cart/items/{id}` 🔒
**Request:** `{ "quantity": 3 }`
Returns full cart.

---

### DELETE `/cart/items/{id}` 🔒
Returns `null` data with success message.

---

### DELETE `/cart` 🔒
Clears entire cart.

---

## 📦 Orders

### POST `/orders` 🔒 — Checkout
**Request:**
```json
{
  "address_id": 2,
  "notes": "الرجاء الاتصال قبل التوصيل"
}
```
**Logic:** Backend calculates subtotal from cart, adds delivery_fee from address zone, creates order, clears cart.

**Response:**
```json
{
  "data": {
    "id": 101,
    "status": "pending",
    "payment_status": "pending",
    "subtotal": 12.500,
    "delivery_fee": 1.500,
    "total": 14.000,
    "notes": "الرجاء الاتصال...",
    "room_id": null,
    "items": [
      { "id": 1, "product_id": 5, "product_name": "كمامة طبية N95", "unit_price": 1.800, "quantity": 2, "total": 3.600 }
    ],
    "address": {
      "id": 2, "label": "المنزل", "address": "عمان، دابوق", "city": "عمان",
      "latitude": 31.99, "longitude": 35.86, "is_default": true,
      "delivery_zone_id": 1,
      "delivery_zone": { "id": 1, "name": "عمان الغربية", "fee": 1.500 }
    },
    "created_at": "2026-08-04T12:30:00.000000Z"
  }
}
```

---

### GET `/orders` 🔒 — Paginated list
### GET `/orders/{id}` 🔒 — Single order

**Order statuses:** `pending` | `confirmed` | `processing` | `shipped` | `delivered` | `cancelled`

---

## 📍 Addresses

### GET `/addresses` 🔒
Returns array of user addresses with delivery zone.

### POST `/addresses` 🔒
**Request:**
```json
{
  "label": "المنزل",
  "address": "عمان، دابوق، شارع الأمير...",
  "city": "عمان",
  "delivery_zone_id": 1,
  "latitude": 31.9906,
  "longitude": 35.8680,
  "is_default": true
}
```
> If `is_default: true` → backend resets all other addresses to false.

### PUT `/addresses/{id}` 🔒 — Update
### DELETE `/addresses/{id}` 🔒 — Delete
### PATCH `/addresses/{id}/default` 🔒 — Set as default (no body needed)

---

## GET `/delivery-zones` (public)
Returns all zones: `[{ "id": 1, "name": "عمان الغربية", "fee": 1.500 }]`
> Load this when user opens address form.

---

## 🩺 Nursing (طلب تمريض)

### GET `/nursing/types` (public)
```json
{
  "data": [
    { "id": 1, "name": "تمريض منزلي", "description": null, "price": 15.000 }
  ]
}
```

### POST `/nursing/requests` 🔒
**Request:**
```json
{
  "type_id": 1,
  "date": "2026-08-10",
  "time": "10:00",
  "address": "عمان، دابوق",
  "notes": "مريض مسن يحتاج مساعدة"
}
```
**Response:** NursingRequest object with status `pending`.

### GET `/nursing/requests` 🔒 — Paginated list
### GET `/nursing/requests/{id}` 🔒 — Single

**Request statuses:** `pending` | `confirmed` | `in_progress` | `completed` | `cancelled`

---

## 🛁 Bathing (طلب استحمام)

### POST `/bathing/redeem` 🔒 — Redeem bathing card
**Request:**
```json
{
  "code": "A3K-9XZ-P2M",
  "date": "2026-08-12",
  "time": "09:00",
  "notes": "مريض مسن"
}
```
> Backend validates: card exists, is_active, used_count < max_uses
> Returns BathingRequest with `payment_type: "card"`
> Error if exhausted: `"البطاقة مستنفذة"`

### GET `/bathing/requests` 🔒 — Paginated

---

## ❤️ Care (طلب رعاية)

### GET `/care/services` (public)
```json
{ "data": [{ "id": 1, "name": "رعاية المسنين", "description": null, "price": 20.000 }] }
```

### POST `/care/requests` 🔒
**Request:**
```json
{
  "date": "2026-08-15",
  "time": "11:00",
  "address": "عمان، الشميساني",
  "notes": "",
  "service_ids": [1, 3, 5]
}
```
> Returns CareRequest with selected services and their snapshotted unit_prices.

### GET `/care/requests` 🔒 — Paginated
### GET `/care/requests/{id}` 🔒

**CareRequest response includes:**
```json
{
  "id": 8, "date": "2026-08-15", "time": "11:00",
  "status": "pending",
  "services": [
    { "id": 1, "name": "رعاية المسنين", "unit_price": 20.000 }
  ]
}
```

---

## 🧪 Lab (مختبر)

### GET `/lab/categories` (public)
### GET `/lab/tests?category_id=2` (public)
```json
{
  "data": [
    { "id": 1, "name": "صورة دم كاملة CBC", "description": null, "price": 8.000, "category": { "id": 2, "name": "تحاليل دم" } }
  ]
}
```

### POST `/lab/requests` 🔒
**Request:**
```json
{
  "date": "2026-08-11",
  "time": "08:00",
  "address_id": 2,
  "notes": "صائم",
  "test_ids": [1, 4, 7]
}
```
> Backend calculates total from test prices (snapshot at booking time).

**Response:**
```json
{
  "id": 15, "date": "2026-08-11", "time": "08:00",
  "status": "pending", "total": 28.500,
  "tests": [
    { "id": 1, "name": "صورة دم كاملة CBC", "unit_price": 8.000 }
  ]
}
```

### GET `/lab/requests` 🔒 — Paginated
### GET `/lab/requests/{id}` 🔒

---

## ☢️ Xray (أشعة)

Identical pattern to Lab — replace `lab` with `xray`, `LabTest` with `XrayTest`:
- `GET /xray/categories` (public)
- `GET /xray/tests?category_id=` (public)
- `POST /xray/requests` 🔒 — same body as lab
- `GET /xray/requests` 🔒
- `GET /xray/requests/{id}` 🔒

---

## 👨‍⚕️ Doctors

### GET `/doctors?search=قلب&specialty=cardiology&page=1` (public)
**Response item:**
```json
{
  "id": 3,
  "name": "د. محمد العلي",
  "photo_url": "https://domain.com/storage/doctors/3.jpg",
  "specialty": "طب القلب",
  "rating": 4.5,
  "home_visit_price": 35.000,
  "appointment_price": 20.000,
  "years_experience": 12,
  "description": "دكتور متخصص في...",
  "booking_phone": "0791112233",
  "is_active": true
}
```

### GET `/doctors/{id}` (public) — Single doctor

### POST `/doctors/{id}/book` 🔒
**Request:**
```json
{
  "visit_type": "home_visit",
  "booking_date": "2026-08-20",
  "booking_time": "14:00",
  "notes": "ألم في الصدر"
}
```
> `visit_type`: `home_visit` or `appointment`
> Price is auto-set from doctor's price based on visit_type.

**Response:**
```json
{
  "id": 22, "visit_type": "home_visit",
  "booking_date": "2026-08-20", "booking_time": "14:00",
  "price": 35.000, "status": "pending",
  "doctor": { "id": 3, "name": "د. محمد العلي", "specialty": "طب القلب", ... }
}
```

### GET `/bookings` 🔒 — All user bookings paginated
### GET `/bookings/{id}` 🔒 — Single booking

**Booking statuses:** `pending` | `confirmed` | `in_progress` | `completed` | `cancelled`

---

## 🥗 Nutrition

### POST `/nutrition/requests` 🔒
**Request:**
```json
{
  "chronic_diseases": "سكري، ضغط",
  "food_allergies": "مكسرات",
  "medicine_allergies": "بنسلين",
  "current_medications": "ميتفورمين",
  "height": 175,
  "weight": 85,
  "goal": "lose_weight",
  "notes": "أريد حمية صحية"
}
```
> `goal`: `lose_weight` | `gain_weight` | `maintain`
> Backend auto-calculates `bmi = weight / (height/100)^2`

**Response:**
```json
{
  "id": 9, "height": 175, "weight": 85, "bmi": 27.76,
  "goal": "lose_weight", "status": "pending",
  "chronic_diseases": "سكري، ضغط",
  "food_allergies": "مكسرات",
  "medicine_allergies": "بنسلين",
  "current_medications": "ميتفورمين",
  "notes": "أريد حمية صحية",
  "created_at": "..."
}
```

### GET `/nutrition/requests` 🔒 — Paginated
### GET `/nutrition/requests/{id}` 🔒

---

## 🚑 Patient Transfer

### POST `/transfers` 🔒
**Request:**
```json
{
  "from_zone_id": 1,
  "to_zone_id": 3,
  "from_location": "مستشفى الأردن، عمان",
  "from_lat": 31.9906,
  "from_lng": 35.8680,
  "to_location": "مستشفى الملك عبدالله، إربد",
  "to_lat": 32.5533,
  "to_lng": 35.8503,
  "case_description": "مريض يحتاج نقل عاجل"
}
```

**Response:**
```json
{
  "id": 5, "status": "pending",
  "from_location": "مستشفى الأردن، عمان",
  "from_lat": 31.9906, "from_lng": 35.8680,
  "to_location": "مستشفى الملك عبدالله، إربد",
  "to_lat": 32.5533, "to_lng": 35.8503,
  "case_description": "...",
  "from_zone": { "id": 1, "name": "عمان الغربية" },
  "to_zone": { "id": 3, "name": "إربد" },
  "created_at": "..."
}
```

### GET `/transfers` 🔒 — Paginated
### GET `/transfers/{id}` 🔒

---

## 📰 Articles

### GET `/articles?search=صحة&page=1` (public)
```json
{
  "data": {
    "data": [
      {
        "id": 1, "title": "فوائد التغذية السليمة",
        "description": "نص المقال الطويل...",
        "image_url": "https://domain.com/storage/articles/1.jpg",
        "published_at": "2026-07-15 09:00"
      }
    ],
    "current_page": 1, "last_page": 3, "total": 45
  }
}
```

### GET `/articles/{id}` (public) — Full article

---

## 💬 Mothers Forum (منتدى الأمهات)

### GET `/forum/categories` (public)
```json
{ "data": [{ "id": 1, "name": "الحمل والولادة", "description": "...", "image_url": "..." }] }
```

### GET `/forum/sub-categories?category_id=1` (public)
```json
{ "data": [{ "id": 2, "name": "الأشهر الأولى", "description": null, "category_id": 1, "category": { "name": "..." } }] }
```

### GET `/forum/posts?sub_category_id=2&type=question&page=1` (public)
`type` = `experience` | `question` (optional filter)

**Response item:**
```json
{
  "id": 10, "title": "ما هو أفضل دكتور للولادة؟",
  "body": "نص السؤال...", "type": "question",
  "is_pinned": false, "replies_count": 14,
  "sub_category_id": 2,
  "user": { "id": 8, "name": "أم أحمد", "phone": "07912..." },
  "created_at": "2026-07-20T10:00:00.000000Z"
}
```
> Pinned posts always come first.

### GET `/forum/posts/{id}` (public) — Post with replies
```json
{
  "id": 10, ...,
  "replies": [
    { "id": 1, "body": "أنا عندي تجربة مع د.محمد...", "user": { "id": 5, "name": "أم سارة" }, "created_at": "..." }
  ]
}
```

### POST `/forum/posts` 🔒
**Request:**
```json
{
  "sub_category_id": 2,
  "title": "سؤالي عن التغذية",
  "body": "تفاصيل السؤال...",
  "type": "question"
}
```

### DELETE `/forum/posts/{id}` 🔒
> Only post owner can delete. Returns 403 otherwise.

### POST `/forum/posts/{id}/replies` 🔒
**Request:** `{ "body": "ردي على هذا الموضوع..." }`
Returns new ForumReply.

### DELETE `/forum/replies/{id}` 🔒
> Only reply owner can delete.

---

## 🏥 Sihati — صحتي (Room System)

This is the core feature. It's a private room for each hospitalized patient where the care team (doctor, nurses, family) communicates and tracks health data.

**Room membership roles:**
- `patient` — the room's patient (determined by `room.patient_id`)
- `doctor` — room member with role=doctor
- `nurse` — room member with role=nurse
- `patient_family` — room member with role=patient_family

**Access:** A user can only access a room if they are the patient OR a room member.

---

### GET `/sihati/my-room` 🔒
> For patient: returns their active room. For others: use roomDetail.

**Response:**
```json
{
  "data": {
    "id": 4, "name": "غرفة أحمد - الرعاية المكثفة",
    "description": "وصف الغرفة...",
    "address": "عمان، مستشفى...",
    "discount_value": "15.00",
    "is_active": true,
    "firebase_room_id": "room_abc123",
    "patient": { "id": 12, "name": "أحمد محمد", "patient_code": "PT-A3F9K821", ... },
    "members": [
      { "id": 1, "user_id": 5, "role": "doctor", "user": { "id": 5, "name": "د. خالد", "phone": "07..." } },
      { "id": 2, "user_id": 8, "role": "nurse",  "user": { "id": 8, "name": "رانيا سالم", "phone": "07..." } }
    ],
    "created_at": "..."
  }
}
```
> `firebase_room_id` — use this to connect to Firebase Realtime Database/Firestore for live chat.
> `discount_value: 15.00` — this room gets 15% discount on lab/xray/store orders linked to it.

---

### GET `/sihati/rooms/{id}` 🔒
Same response as my-room. Also includes `activeAssignment.template`.

---

### GET `/sihati/rooms/{id}/reports?page=1` 🔒 — Paginated

**Response item:**
```json
{
  "id": 33, "report_type": "nurse",
  "submitted_at": "2026-08-04T14:00:00.000000Z",
  "submitted_by": { "id": 8, "name": "رانيا سالم" }
}
```
`report_type`: `registration` | `nurse` | `doctor`

---

### POST `/sihati/rooms/{id}/reports` 🔒 — Submit Report

**How it works:**
1. Backend finds the active template assignment for the room
2. The template has fields with different answer types
3. You send answers keyed by `field_id`

**Request (multipart/form-data for image answers):**
```
answers[1] = "120/80"           ← text field (field id=1)
answers[2] = 98.6               ← number field (field id=2)  
answers[3] = true               ← yes_no field (field id=3)
answers[4] = [binary file]      ← image field (field id=4)
```

**Response:**
```json
{
  "id": 34, "report_type": "nurse",
  "submitted_at": "2026-08-04T15:00:00.000000Z",
  "submitted_by": { "id": 8, "name": "رانيا سالم" },
  "answers": [
    { "id": 1, "field_question": "ضغط الدم؟", "field_answer_type": "text", "display_answer": "120/80" },
    { "id": 2, "field_question": "درجة الحرارة؟", "field_answer_type": "number", "display_answer": "98.6" },
    { "id": 3, "field_question": "المريض واعٍ؟", "field_answer_type": "yes_no", "display_answer": "نعم" },
    { "id": 4, "field_question": "صورة الجرح", "field_answer_type": "image", "display_answer": "https://domain.com/storage/room_reports/img.jpg" }
  ]
}
```
> `display_answer` is always a human-readable string. Use it directly in UI.
> For `yes_no` → backend stores boolean, returns "نعم" / "لا"
> For `image` → returns full URL

---

### GET `/sihati/rooms/{id}/reports/{reportId}` 🔒
Single report with full answers array.

---

### GET `/sihati/rooms/{id}/doctor-orders?page=1` 🔒
```json
{
  "data": {
    "data": [
      {
        "id": 7, "order_text": "إعطاء المريض 500mg باراسيتامول كل 8 ساعات",
        "is_executed": false, "executed_at": null,
        "doctor": { "id": 5, "name": "د. خالد" },
        "replies": [
          { "id": 1, "reply_text": "تم تنفيذ الأمر في الساعة 2PM", "nurse": { "id": 8, "name": "رانيا سالم" }, "created_at": "..." }
        ],
        "created_at": "2026-08-04T09:00:00.000000Z"
      }
    ]
  }
}
```

---

### POST `/sihati/rooms/{id}/doctor-orders/{orderId}/reply` 🔒
> **Only nurses** can call this (403 otherwise).

**Request:** `{ "reply_text": "تم تنفيذ الأمر في الساعة 3PM" }`
Returns updated DoctorOrder with all replies.

---

### GET `/sihati/rooms/{id}/medications` 🔒
```json
{
  "data": [
    {
      "id": 3, "medication_name": "أموكسيسيلين",
      "dosage": "500mg", "frequency": "3 مرات يومياً",
      "start_date": "2026-08-01", "end_date": "2026-08-10",
      "notes": null,
      "added_by": { "id": 5, "name": "د. خالد" }
    }
  ]
}
```

---

### POST `/sihati/rooms/{id}/medications` 🔒
**Request:**
```json
{
  "medication_name": "أموكسيسيلين",
  "dosage": "500mg",
  "frequency": "3 مرات يومياً",
  "start_date": "2026-08-01",
  "end_date": "2026-08-10",
  "notes": "مع الطعام"
}
```

---

### GET `/sihati/documents/{type}` (public)
`type` = `authorization` | `pledge`

```json
{
  "data": {
    "id": 1,
    "type": "authorization",
    "title": "وثيقة التفويض",
    "content": "أنا الموقع أدناه {patient_name}، أفوض..."
  }
}
```
> Replace `{patient_name}`, `{room_name}`, `{date}` in content with actual values before displaying.

---

## 💊 Medications — Outside Rooms (Patient only)

### GET `/medications` 🔒 — Paginated
### POST `/medications` 🔒
**Request:**
```json
{
  "medication_name": "ميتفورمين",
  "dosage": "1000mg",
  "frequency": "مرتين يومياً مع الطعام",
  "start_date": "2026-01-01",
  "end_date": "2026-12-31",
  "notes": "لمرض السكري"
}
```
### DELETE `/medications/{id}` 🔒

---

## 🎨 Design System — "World Class" UI

### Design Philosophy
- **Arabic RTL** — ALL screens must be RTL (set `textDirection: TextDirection.rtl` globally)
- **Neumorphism + Glassmorphism** hybrid — soft shadows, blurred glass cards
- **Medical but warm** — not clinical/cold, feel like a caring companion
- **Micro-animations everywhere** — every tap, transition, loading must feel alive

### Color Palette
```dart
class AppColors {
  // Primary brand
  static const primary     = Color(0xFF1B8B5E);  // Deep medical green
  static const primaryLight= Color(0xFF4CAF50);
  static const secondary   = Color(0xFF0D6EAF);  // Trust blue

  // Gradients
  static const gradientStart = Color(0xFF1B8B5E);
  static const gradientEnd   = Color(0xFF0D6EAF);

  // Backgrounds
  static const background    = Color(0xFFF5F7FA);
  static const cardBg        = Color(0xFFFFFFFF);
  static const darkBg        = Color(0xFF0D1B2A);

  // Semantic
  static const success  = Color(0xFF27AE60);
  static const warning  = Color(0xFFF39C12);
  static const error    = Color(0xFFE74C3C);
  static const info     = Color(0xFF3498DB);

  // Text
  static const textPrimary   = Color(0xFF1A1A2E);
  static const textSecondary = Color(0xFF6B7280);
  static const textHint      = Color(0xFFBDBDBD);
}
```

### Typography (Google Fonts: Cairo for Arabic)
```dart
class AppTextStyles {
  static const fontFamily = 'Cairo';

  static TextStyle heading1 = TextStyle(fontSize: 28, fontWeight: FontWeight.w800, color: AppColors.textPrimary);
  static TextStyle heading2 = TextStyle(fontSize: 22, fontWeight: FontWeight.w700, color: AppColors.textPrimary);
  static TextStyle heading3 = TextStyle(fontSize: 18, fontWeight: FontWeight.w600, color: AppColors.textPrimary);
  static TextStyle bodyLarge  = TextStyle(fontSize: 16, fontWeight: FontWeight.w400, color: AppColors.textPrimary);
  static TextStyle bodyMedium = TextStyle(fontSize: 14, fontWeight: FontWeight.w400, color: AppColors.textSecondary);
  static TextStyle caption    = TextStyle(fontSize: 12, fontWeight: FontWeight.w400, color: AppColors.textHint);
  static TextStyle button     = TextStyle(fontSize: 16, fontWeight: FontWeight.w700, color: Colors.white);
}
```

### Border Radius & Shadows
```dart
class AppSizes {
  static const radiusSmall  = 8.0;
  static const radiusMedium = 16.0;
  static const radiusLarge  = 24.0;
  static const radiusXL     = 32.0;
  static const radiusCircle = 100.0;

  static const paddingSm = 8.0;
  static const paddingMd = 16.0;
  static const paddingLg = 24.0;
}

BoxShadow softShadow = BoxShadow(
  color: Colors.black.withOpacity(0.06),
  blurRadius: 20, offset: Offset(0, 4),
);
BoxShadow coloredShadow = BoxShadow(
  color: AppColors.primary.withOpacity(0.3),
  blurRadius: 15, offset: Offset(0, 6),
);
```

### Must-Have UI Components

#### 1. Gradient Button (for all primary actions)
```dart
class GreenButton extends StatelessWidget {
  // Full-width gradient button with ripple + scale animation on press
  // LinearGradient from primary to secondary
  // BoxShadow with colored shadow
  // Loading spinner that replaces text when isLoading=true
}
```

#### 2. Medical Card (neumorphic)
```dart
class MedicalCard extends StatelessWidget {
  // White background, radius 20, soft shadow top-left + bottom-right
  // Subtle green left border (4px)
  // Slide-in animation on first render
}
```

#### 3. Status Badge
```dart
Widget statusBadge(String status) {
  // pending  → amber background, "بانتظار التأكيد"
  // confirmed → blue, "مؤكد"
  // in_progress → purple, "قيد التنفيذ"
  // completed → green, "مكتمل"
  // cancelled → red, "ملغي"
  // delivered → teal, "تم التوصيل"
}
```

#### 4. Loading Skeleton
```dart
class SkeletonLoader extends StatelessWidget {
  // Use shimmer package
  // Match the layout of the target card/list item
  // Animate with shimmer effect
}
```

#### 5. Empty State Widget
```dart
class EmptyState extends StatelessWidget {
  final String lottieAsset;   // use Lottie animations
  final String title;
  final String? subtitle;
  final Widget? action;
}
```

#### 6. Error Widget
```dart
class AppErrorWidget extends StatelessWidget {
  final String message;
  final VoidCallback onRetry;
  // Show error icon + message + "إعادة المحاولة" button
}
```

---

## 📱 App Screens & Navigation

### Navigation Structure (GoRouter)
```
/splash              → SplashPage (check token, redirect)
/login               → PhonePage
/otp                 → OtpPage
/setup-profile       → ProfileSetupPage (new users only)
/home                → HomePage (BottomNavBar)
  /home/tab-home     → HomeTab
  /home/tab-store    → StoreTab
  /home/tab-services → ServicesTab
  /home/tab-sihati   → SihatiTab
  /home/tab-profile  → ProfileTab
/banners             → (embedded in home)
/store/categories    → CategoriesPage
/store/products      → ProductsPage
/store/products/:id  → ProductDetailPage
/cart                → CartPage
/checkout            → CheckoutPage
/orders              → OrdersPage
/orders/:id          → OrderDetailPage
/addresses           → AddressesPage
/nursing             → NursingPage
/bathing             → BathingPage
/care                → CarePage
/lab                 → LabPage
/xray                → XrayPage
/doctors             → DoctorsPage
/doctors/:id         → DoctorDetailPage
/nutrition           → NutritionPage
/transfer            → TransferPage
/articles            → ArticlesPage
/articles/:id        → ArticleDetailPage
/forum               → ForumCategoriesPage
/forum/:categoryId   → SubCategoriesPage
/forum/posts         → PostsPage
/forum/posts/:id     → PostDetailPage
/medications         → MedicationsPage
/sihati/room         → RoomPage (tabbed: الغرفة | التقارير | الأدوية | الأوامر)
/sihati/reports/:id  → ReportDetailPage
/profile             → ProfilePage
```

### Bottom Navigation Bar
```
🏠 الرئيسية  |  🛒 المتجر  |  ➕ الخدمات  |  🏥 صحتي  |  👤 حسابي
```
- Center "+" button (Services) with elevated FAB style, gradient background
- Active tab: green color + slight scale-up animation
- Unactive: gray

---

## 🏠 Home Screen Design

**Layout (top to bottom):**
1. **Header** — wave shape at top, "مرحباً {name} 👋" greeting, green gradient
2. **Banner Slider** — PageView with auto-scroll, dot indicators
3. **Patient Code Card** — if patient role: show `PT-XXXXXX` in a special card with copy button
4. **Quick Services Grid** — 2x4 grid of service cards (nursing, bathing, care, lab, xray, doctors, nutrition, transfer)
5. **Latest Articles** — horizontal scroll, card with image overlay title
6. **Forum Preview** — latest 3 posts as cards

---

## 🏥 Sihati (صحتي) — Special Screen

This is the most important and complex screen. Design it as a **hospital dashboard**.

**Tabs:**
1. **الغرفة** — Room info card, members list (doctor/nurse/family chips), discount badge, active template name
2. **التقارير** — List of submitted reports. Each item shows type badge (تمريض/دكتور), submitter, time. Tap to view full report with answers displayed beautifully.
3. **الأدوية** — List of room medications. Each card shows: pill icon, medication name, dosage, frequency, date range, who added it.
4. **الأوامر الطبية** — Doctor orders list. Each order shows: doctor name, order text, execution status (مُنفَّذ / بانتظار). Expandable to show nurse replies. If current user is nurse → show reply button.

**Submit Report Flow:**
- Tap "تقديم تقرير"
- Backend returns active template with fields
- Display each field as appropriate input:
  - `text` → TextFormField
  - `number` → TextFormField with numeric keyboard
  - `yes_no` → Toggle switch or two-button selector
  - `image` → ImagePicker with preview
- Submit → show confirmation animation → refresh reports list

---

## 🔄 BLoC Examples

### AuthBloc
```dart
// Events
class SendOtpEvent extends AuthEvent { final String phone; }
class VerifyOtpEvent extends AuthEvent { final String phone; final String otp; }
class LogoutEvent extends AuthEvent {}

// States
class AuthInitial extends AuthState {}
class AuthLoading extends AuthState {}
class OtpSentState extends AuthState { final bool isNewUser; }
class AuthenticatedState extends AuthState { final UserEntity user; }
class AuthErrorState extends AuthState { final String message; }
```

### Generic List Pattern
```dart
// State
class ProductsState extends Equatable {
  final List<ProductEntity> products;
  final bool isLoading;
  final String? error;
  final bool hasMore;
  final int currentPage;
}

// Bloc
class ProductsBloc extends Bloc<ProductsEvent, ProductsState> {
  on<LoadProductsEvent>(_onLoad);
  on<LoadMoreProductsEvent>(_onLoadMore);
  on<SearchProductsEvent>(_onSearch);
}
```

---

## 🧹 Code Quality Rules

1. **No logic in widgets** — widgets only call BLoC events and render state
2. **Every string in Arabic** — create `AppStrings` class, no hardcoded Arabic in widgets
3. **Every color/size from constants** — no magic numbers
4. **Models are immutable** — use `copyWith` + `const` constructors
5. **Entities have no framework dependencies** — pure Dart
6. **Use `Either<Failure, T>`** in all repository/usecase return types
7. **Error messages from API** — always show `response.message` from the JSON, never hardcode error text
8. **Images**: always use `CachedNetworkImage` with shimmer placeholder
9. **Pull to refresh** on all list screens
10. **Infinite scroll pagination** — load more when user reaches bottom (use `last_page` + `current_page` from API)
11. **Optimistic UI** — for cart: update UI immediately, revert on error
12. **Token refresh**: if 401 → clear token → push to login using GoRouter

---

## 🚀 Launch Checklist

- [ ] Splash screen with Green Medical logo + fade-in animation (3s)
- [ ] RTL direction set globally in `MaterialApp`
- [ ] Dark mode support (optional but impressive)
- [ ] Arabic locale (`Locale('ar', 'JO')`)
- [ ] FCM setup → call `PUT /auth/fcm-token` on every login
- [ ] Handle app lifecycle → refresh token check on foreground
- [ ] `flutter_secure_storage` for token (not SharedPreferences)
- [ ] Environment config (dev/prod base URLs)
- [ ] All images have error widget fallback
- [ ] All lists have empty state widget
- [ ] All async actions have loading indicators
- [ ] Form validation with Arabic messages before API call
- [ ] Haptic feedback on important actions

---

## 📋 Quick API Reference

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/auth/send-otp` | ❌ | إرسال OTP |
| POST | `/auth/verify-otp` | ❌ | تسجيل الدخول |
| POST | `/auth/logout` | ✅ | تسجيل الخروج |
| GET | `/auth/me` | ✅ | بيانات المستخدم |
| PUT | `/auth/profile` | ✅ | تعديل الملف الشخصي |
| PUT | `/auth/fcm-token` | ✅ | تحديث FCM |
| GET | `/banners?section=` | ❌ | البنرات |
| GET | `/delivery-zones` | ❌ | مناطق التوصيل |
| GET | `/store/categories` | ❌ | تصنيفات المتجر |
| GET | `/store/products` | ❌ | منتجات + بحث |
| GET | `/store/products/{id}` | ❌ | تفاصيل منتج |
| GET | `/store/categories/{id}/products` | ❌ | منتجات حسب تصنيف |
| GET | `/addresses` | ✅ | عناويني |
| POST | `/addresses` | ✅ | إضافة عنوان |
| PUT | `/addresses/{id}` | ✅ | تعديل عنوان |
| DELETE | `/addresses/{id}` | ✅ | حذف عنوان |
| PATCH | `/addresses/{id}/default` | ✅ | تعيين كافتراضي |
| GET | `/cart` | ✅ | السلة |
| POST | `/cart/items` | ✅ | إضافة للسلة |
| PATCH | `/cart/items/{id}` | ✅ | تعديل الكمية |
| DELETE | `/cart/items/{id}` | ✅ | حذف من السلة |
| DELETE | `/cart` | ✅ | تفريغ السلة |
| POST | `/orders` | ✅ | إتمام الطلب |
| GET | `/orders` | ✅ | طلباتي |
| GET | `/orders/{id}` | ✅ | تفاصيل طلب |
| GET | `/nursing/types` | ❌ | أنواع التمريض |
| POST | `/nursing/requests` | ✅ | طلب تمريض |
| GET | `/nursing/requests` | ✅ | طلباتي (تمريض) |
| GET | `/nursing/requests/{id}` | ✅ | تفاصيل طلب |
| POST | `/bathing/redeem` | ✅ | استخدام بطاقة استحمام |
| GET | `/bathing/requests` | ✅ | طلباتي (استحمام) |
| GET | `/care/services` | ❌ | خدمات الرعاية |
| POST | `/care/requests` | ✅ | طلب رعاية |
| GET | `/care/requests` | ✅ | طلباتي (رعاية) |
| GET | `/care/requests/{id}` | ✅ | تفاصيل طلب |
| GET | `/lab/categories` | ❌ | فئات التحاليل |
| GET | `/lab/tests` | ❌ | التحاليل |
| POST | `/lab/requests` | ✅ | طلب تحاليل |
| GET | `/lab/requests` | ✅ | طلباتي (تحاليل) |
| GET | `/lab/requests/{id}` | ✅ | تفاصيل طلب |
| GET | `/xray/categories` | ❌ | فئات الأشعة |
| GET | `/xray/tests` | ❌ | الأشعة |
| POST | `/xray/requests` | ✅ | طلب أشعة |
| GET | `/xray/requests` | ✅ | طلباتي (أشعة) |
| GET | `/xray/requests/{id}` | ✅ | تفاصيل طلب |
| GET | `/doctors` | ❌ | الأطباء + بحث |
| GET | `/doctors/{id}` | ❌ | تفاصيل طبيب |
| POST | `/doctors/{id}/book` | ✅ | حجز طبيب |
| GET | `/bookings` | ✅ | حجوزاتي |
| GET | `/bookings/{id}` | ✅ | تفاصيل حجز |
| POST | `/nutrition/requests` | ✅ | طلب تغذية |
| GET | `/nutrition/requests` | ✅ | طلبات التغذية |
| GET | `/nutrition/requests/{id}` | ✅ | تفاصيل طلب |
| POST | `/transfers` | ✅ | طلب نقل مريض |
| GET | `/transfers` | ✅ | طلبات النقل |
| GET | `/transfers/{id}` | ✅ | تفاصيل طلب |
| GET | `/articles` | ❌ | المقالات |
| GET | `/articles/{id}` | ❌ | تفاصيل مقال |
| GET | `/forum/categories` | ❌ | أقسام المنتدى |
| GET | `/forum/sub-categories` | ❌ | الأقسام الفرعية |
| GET | `/forum/posts` | ❌ | المنشورات |
| GET | `/forum/posts/{id}` | ❌ | تفاصيل منشور + ردوده |
| POST | `/forum/posts` | ✅ | إضافة منشور |
| DELETE | `/forum/posts/{id}` | ✅ | حذف منشورك |
| POST | `/forum/posts/{id}/replies` | ✅ | إضافة رد |
| DELETE | `/forum/replies/{id}` | ✅ | حذف ردك |
| GET | `/sihati/my-room` | ✅ | غرفتي النشطة |
| GET | `/sihati/rooms/{id}` | ✅ | تفاصيل الغرفة |
| GET | `/sihati/rooms/{id}/reports` | ✅ | تقارير الغرفة |
| POST | `/sihati/rooms/{id}/reports` | ✅ | تقديم تقرير |
| GET | `/sihati/rooms/{id}/reports/{rid}` | ✅ | تفاصيل تقرير |
| GET | `/sihati/rooms/{id}/doctor-orders` | ✅ | الأوامر الطبية |
| POST | `/sihati/rooms/{id}/doctor-orders/{oid}/reply` | ✅ | رد الممرض (nurses only) |
| GET | `/sihati/rooms/{id}/medications` | ✅ | أدوية الغرفة |
| POST | `/sihati/rooms/{id}/medications` | ✅ | إضافة دواء |
| GET | `/sihati/documents/{type}` | ❌ | وثيقة تفويض/تعهد |
| GET | `/medications` | ✅ | أدويتي الشخصية |
| POST | `/medications` | ✅ | إضافة دواء شخصي |
| DELETE | `/medications/{id}` | ✅ | حذف دواء |

---

## 💡 Final Notes

- App name in Arabic: **"Green الطبي"**
- App supports role-based UI: `patient`, `nurse`, `doctor`, `patient_family` see different content in Sihati
- `firebase_room_id` from the Room response → connect to Firebase for real-time chat inside the room
- The discount system: when a room has `discount_value > 0`, the patient's lab/xray/store orders linked to that room get that % discount (handled server-side, just show discounted price to user)
- User `role` can be: `patient`, `nurse`, `super_nurse`, `doctor`, `patient_family`, `university_manager`
- Always build for Arabic first — all text, layouts, and icons should feel native Arabic
