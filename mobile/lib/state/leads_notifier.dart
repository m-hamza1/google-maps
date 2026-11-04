import 'package:flutter/material.dart';
import '../models/lead.dart';
import '../models/lead_stats.dart';
import '../models/pagination_meta.dart';
import '../services/api_service.dart';

class LeadsNotifier extends ChangeNotifier {
  final ApiService _apiService;

  LeadsNotifier({ApiService? apiService}) : _apiService = apiService ?? ApiService();

  List<Lead> _leads = [];
  PaginationMeta _pagination = PaginationMeta.empty();
  LeadStats _stats = LeadStats.empty();
  List<String> _categories = [];
  List<String> _cities = [];

  bool _isLoading = false;
  bool _isLoadingMore = false;
  String? _errorMessage;

  // Filter state
  String _searchTerm = '';
  String _selectedStatus = 'all';
  String _selectedCategory = 'all';
  String _selectedCity = 'all';
  double? _minRating;
  bool _onlyWithPhone = false;

  // Getters
  List<Lead> get leads => _leads;
  PaginationMeta get pagination => _pagination;
  LeadStats get stats => _stats;
  List<String> get categories => _categories;
  List<String> get cities => _cities;
  bool get isLoading => _isLoading;
  bool get isLoadingMore => _isLoadingMore;
  String? get errorMessage => _errorMessage;

  String get searchTerm => _searchTerm;
  String get selectedStatus => _selectedStatus;
  String get selectedCategory => _selectedCategory;
  String get selectedCity => _selectedCity;
  double? get minRating => _minRating;
  bool get onlyWithPhone => _onlyWithPhone;

  bool get hasActiveFilters =>
      _searchTerm.isNotEmpty ||
      _selectedStatus != 'all' ||
      _selectedCategory != 'all' ||
      _selectedCity != 'all' ||
      (_minRating != null && _minRating! > 0) ||
      _onlyWithPhone;

  Future<void> init() async {
    await Future.wait([
      fetchLeads(resetPage: true),
      refreshStats(),
      refreshCategories(),
      refreshCities(),
    ]);
  }

  Future<void> fetchLeads({bool resetPage = false}) async {
    if (resetPage) {
      _isLoading = true;
      _errorMessage = null;
      notifyListeners();
    } else {
      if (_isLoadingMore || !_pagination.hasMore) return;
      _isLoadingMore = true;
      notifyListeners();
    }

    final page = resetPage ? 1 : _pagination.currentPage + 1;

    try {
      final result = await _apiService.fetchLeads(
        search: _searchTerm,
        status: _selectedStatus,
        category: _selectedCategory,
        city: _selectedCity != 'all' ? _selectedCity : null,
        minRating: _minRating,
        hasPhone: _onlyWithPhone ? true : null,
        page: page,
      );

      if (resetPage) {
        _leads = result.leads;
      } else {
        _leads.addAll(result.leads);
      }
      _pagination = result.pagination;
      _errorMessage = null;
    } catch (e) {
      _errorMessage = e.toString().replaceFirst('Exception: ', '');
    } finally {
      _isLoading = false;
      _isLoadingMore = false;
      notifyListeners();
    }
  }

  Future<void> refreshStats() async {
    try {
      _stats = await _apiService.fetchStats();
      notifyListeners();
    } catch (_) {}
  }

  Future<void> refreshCategories() async {
    try {
      _categories = await _apiService.fetchCategories();
      notifyListeners();
    } catch (_) {}
  }

  Future<void> refreshCities() async {
    try {
      _cities = await _apiService.fetchCities();
      notifyListeners();
    } catch (_) {}
  }

  void setSearch(String query) {
    if (_searchTerm == query) return;
    _searchTerm = query;
    fetchLeads(resetPage: true);
  }

  void setStatus(String status) {
    if (_selectedStatus == status) return;
    _selectedStatus = status;
    fetchLeads(resetPage: true);
  }

  void setCategory(String category) {
    if (_selectedCategory == category) return;
    _selectedCategory = category;
    fetchLeads(resetPage: true);
  }

  void setCity(String city) {
    if (_selectedCity == city) return;
    _selectedCity = city;
    fetchLeads(resetPage: true);
  }

  void setMinRating(double? rating) {
    if (_minRating == rating) return;
    _minRating = rating;
    fetchLeads(resetPage: true);
  }

  void setOnlyWithPhone(bool value) {
    if (_onlyWithPhone == value) return;
    _onlyWithPhone = value;
    fetchLeads(resetPage: true);
  }

  void resetFilters() {
    _searchTerm = '';
    _selectedStatus = 'all';
    _selectedCategory = 'all';
    _selectedCity = 'all';
    _minRating = null;
    _onlyWithPhone = false;
    fetchLeads(resetPage: true);
  }

  /// Updates lead with optimistic UI and syncs with backend
  Future<bool> updateLead(
    int leadId, {
    String? secondaryPhone,
    String? notes,
    String? status,
  }) async {
    final index = _leads.indexWhere((l) => l.id == leadId);
    if (index == -1) return false;

    final oldLead = _leads[index];
    final updatedLead = oldLead.copyWith(
      secondaryPhone: secondaryPhone,
      notes: notes,
      status: status,
    );

    // Optimistic update
    _leads[index] = updatedLead;
    notifyListeners();

    try {
      final remoteLead = await _apiService.updateLead(
        leadId,
        secondaryPhone: secondaryPhone,
        notes: notes,
        status: status,
      );
      _leads[index] = remoteLead;
      refreshStats();
      notifyListeners();
      return true;
    } catch (e) {
      // Revert on error
      _leads[index] = oldLead;
      notifyListeners();
      return false;
    }
  }
}
