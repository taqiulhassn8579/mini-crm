# 📥 Postman Import Guide

## 🎯 Overview

This guide will help you import and set up the Mini CRM API collection in Postman for testing all endpoints.

## 📦 Files to Import

1. **`Mini_CRM_API.postman_collection.json`** - Main API collection
2. **`Mini_CRM_API.postman_environment.json`** - Environment variables

## 🚀 Step-by-Step Import

### **Step 1: Import Collection**
1. Open **Postman**
2. Click **"Import"** button (top left)
3. Drag & drop `Mini_CRM_API.postman_collection.json` or click "Upload Files"
4. Click **"Import"**

### **Step 2: Import Environment**
1. Click **"Import"** again
2. Drag & drop `Mini_CRM_API.postman_environment.json`
3. Click **"Import"**

### **Step 3: Select Environment**
1. In the top-right corner, click the environment dropdown
2. Select **"Mini CRM - Local Development"**

## ⚙️ Environment Variables

The environment includes these pre-configured variables:

| Variable | Value | Description |
|----------|-------|-------------|
| `base_url` | `http://localhost:8000` | Your Laravel app URL |
| `auth_token` | (auto-filled) | JWT token from login |
| `user_role` | (auto-filled) | Current user's role |
| `user_name` | (auto-filled) | Current user's name |
| `admin_email` | `admin@example.com` | Admin login email |
| `admin_password` | `password` | Admin login password |
| `agent_email` | `agent@example.com` | Agent login email |
| `agent_password` | `password` | Agent login password |
| `test_lead_id` | `1` | Test lead ID for operations |
| `test_agent_id` | `2` | Test agent ID for assignments |

## 🔐 Testing Authentication

### **1. Login as Admin**
1. Go to **"🔐 Authentication"** → **"Login"**
2. Update the request body with admin credentials:
```json
{
    "email": "{{admin_email}}",
    "password": "{{admin_password}}"
}
```
3. Click **"Send"**
4. ✅ **Auto-save**: Token is automatically saved to `auth_token` variable

### **2. Login as Agent**
1. Update the request body with agent credentials:
```json
{
    "email": "{{agent_email}}",
    "password": "{{agent_password}}"
}
```
2. Click **"Send"**
3. ✅ **Auto-save**: Token is automatically saved

## 📊 Testing Lead Management

### **Prerequisites**
- ✅ Laravel app running on `http://localhost:8000`
- ✅ Database migrated and seeded
- ✅ User authenticated (token saved)

### **1. Create Lead (Admin Only)**
1. Go to **"📊 Leads Management"** → **"Create Lead"**
2. The request body is pre-filled with example data
3. Click **"Send"**
4. ✅ **Result**: New lead created, agent notified

### **2. Get All Leads**
1. Go to **"📊 Leads Management"** → **"Get All Leads"**
2. Try different query parameters:
   - `status=new`
   - `search=john`
   - `sort_by=created_at&sort_direction=desc`
3. Click **"Send"**

### **3. Get Specific Lead**
1. Go to **"📊 Leads Management"** → **"Get Lead by ID"**
2. Update the URL: `/api/leads/{{test_lead_id}}`
3. Click **"Send"**

### **4. Update Lead**
1. Go to **"📊 Leads Management"** → **"Update Lead"**
2. Update the URL: `/api/leads/{{test_lead_id}}`
3. Modify the request body as needed
4. Click **"Send"**

### **5. Delete Lead (Admin Only)**
1. Go to **"📊 Leads Management"** → **"Delete Lead"**
2. Update the URL: `/api/leads/{{test_lead_id}}`
3. Click **"Send"**

## 🔄 Testing Different User Roles

### **Admin User Testing**
1. **Login** with admin credentials
2. **Create** new leads
3. **View** all leads (unfiltered)
4. **Update** any lead
5. **Delete** leads
6. **Assign** leads to agents

### **Agent User Testing**
1. **Login** with agent credentials
2. **View** only assigned leads
3. **Update** assigned leads
4. **Cannot** create/delete leads
5. **Cannot** assign leads

## 🧪 Testing Scenarios

### **Scenario 1: Lead Assignment Flow**
1. Login as admin
2. Create a new lead assigned to an agent
3. Check agent receives email notification
4. Login as agent
5. Verify agent can see the assigned lead

### **Scenario 2: Lead Reassignment**
1. Login as admin
2. Update lead assignment to different agent
3. Check new agent receives notification
4. Verify old agent no longer sees the lead

### **Scenario 3: Role-Based Access**
1. Login as agent
2. Try to access admin-only endpoints
3. Verify 403 Forbidden responses
4. Test agent-only access to assigned leads

## 📋 Request Examples

### **Create Lead Request**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "phone": "555-987-6543",
    "status": "new",
    "assigned_to": 2,
    "notes": "Interested in enterprise solution. High priority lead."
}
```

### **Update Lead Request**
```json
{
    "name": "Jane Smith",
    "email": "jane.smith@example.com",
    "phone": "555-987-6543",
    "status": "contacted",
    "assigned_to": 2,
    "notes": "Contacted via email. Scheduled demo for next week."
}
```

### **Query Parameters Examples**
```
# Filter by status
/api/leads?status=new

# Search by name/email
/api/leads?search=jane

# Sort by creation date
/api/leads?sort_by=created_at&sort_direction=desc

# Pagination
/api/leads?page=1&per_page=5

# Combined filters
/api/leads?status=new&search=jane&sort_by=created_at&sort_direction=desc&page=1&per_page=10
```

## 🚨 Common Issues & Solutions

### **Issue 1: "Could not get any response"**
- ✅ Check if Laravel app is running
- ✅ Verify `base_url` is correct
- ✅ Check firewall/network settings

### **Issue 2: "401 Unauthorized"**
- ✅ Login first to get auth token
- ✅ Check if token is expired
- ✅ Verify token is in `Authorization` header

### **Issue 3: "403 Forbidden"**
- ✅ Check user role permissions
- ✅ Verify user can access the resource
- ✅ Test with admin account first

### **Issue 4: "422 Validation Error"**
- ✅ Check request body format
- ✅ Verify required fields are present
- ✅ Check field validation rules

## 🔍 Debugging Tips

### **1. Check Console Logs**
- Open Postman Console (View → Show Postman Console)
- Monitor request/response details
- Check for auto-saved variables

### **2. Verify Environment Variables**
- Click the eye icon next to environment selector
- Verify all variables are set correctly
- Check if `auth_token` is populated after login

### **3. Test Individual Endpoints**
- Start with simple GET requests
- Test authentication separately
- Verify each endpoint step by step

## 📚 Additional Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Postman Learning Center**: https://learning.postman.com
- **API Testing Best Practices**: https://www.postman.com/collection/guides

## 🎉 Success Checklist

- ✅ Collection imported successfully
- ✅ Environment variables configured
- ✅ Laravel app running and accessible
- ✅ Database migrated and seeded
- ✅ Admin user can login and create leads
- ✅ Agent user can login and view assigned leads
- ✅ Notifications working (check email logs)
- ✅ All CRUD operations functional
- ✅ Role-based access control working

---

**Happy API Testing! 🚀**

If you encounter any issues, check the Laravel logs at `storage/logs/laravel.log` and Postman console for detailed error messages.
