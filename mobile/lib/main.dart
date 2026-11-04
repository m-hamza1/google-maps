import 'package:flutter/material.dart';
import 'config/app_config.dart';
import 'screens/analytics_screen.dart';
import 'screens/leads_feed_screen.dart';
import 'screens/saved_leads_screen.dart';
import 'state/leads_notifier.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const LeadRadarApp());
}

class LeadRadarApp extends StatefulWidget {
  const LeadRadarApp({super.key});

  @override
  State<LeadRadarApp> createState() => _LeadRadarAppState();
}

class _LeadRadarAppState extends State<LeadRadarApp> {
  final LeadsNotifier _notifier = LeadsNotifier();

  @override
  void initState() {
    super.initState();
    _notifier.init();
  }

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'LeadRadar',
      debugShowCheckedModeBanner: false,
      theme: AppConfig.darkTheme,
      home: MainNavigationScaffold(notifier: _notifier),
    );
  }
}

class MainNavigationScaffold extends StatefulWidget {
  final LeadsNotifier notifier;

  const MainNavigationScaffold({super.key, required this.notifier});

  @override
  State<MainNavigationScaffold> createState() => _MainNavigationScaffoldState();
}

class _MainNavigationScaffoldState extends State<MainNavigationScaffold> {
  int _currentIndex = 0;

  @override
  Widget build(BuildContext context) {
    final screens = [
      LeadsFeedScreen(notifier: widget.notifier),
      SavedLeadsScreen(notifier: widget.notifier),
      AnalyticsScreen(notifier: widget.notifier),
    ];

    return Scaffold(
      body: IndexedStack(
        index: _currentIndex,
        children: screens,
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: _currentIndex,
        backgroundColor: AppConfig.backgroundColor,
        indicatorColor: AppConfig.primaryColor.withValues(alpha: 0.2),
        onDestinationSelected: (idx) {
          setState(() => _currentIndex = idx);
        },
        destinations: const [
          NavigationDestination(
            icon: Icon(Icons.explore_outlined),
            selectedIcon: Icon(Icons.explore, color: AppConfig.primaryColor),
            label: 'All Leads',
          ),
          NavigationDestination(
            icon: Icon(Icons.bookmark_border),
            selectedIcon: Icon(Icons.bookmark, color: AppConfig.primaryColor),
            label: 'Actioned',
          ),
          NavigationDestination(
            icon: Icon(Icons.pie_chart_outline),
            selectedIcon: Icon(Icons.pie_chart, color: AppConfig.primaryColor),
            label: 'Analytics',
          ),
        ],
      ),
    );
  }
}
