import 'package:flutter_test/flutter_test.dart';
import 'package:mobile/main.dart';

void main() {
  testWidgets('LeadRadarApp smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const LeadRadarApp());
    expect(find.byType(LeadRadarApp), findsOneWidget);
  });
}
