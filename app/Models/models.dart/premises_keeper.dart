import 'package:isar/isar.dart';

part 'premises_keeper.g.dart';

@Collection()
class PremisesKeeper {
  Id id = Isar.autoIncrement;

  int? premisesId;
  int? personId;
  DateTime? startDate;
  DateTime? endDate;
  String? uid;
  int? version;

  final premise = IsarLink<dynamic>();
  final person = IsarLink<dynamic>();

  PremisesKeeper(
      {this.premisesId,
      this.personId,
      this.startDate,
      this.endDate,
      this.uid,
      this.version});

  factory PremisesKeeper.fromJson(Map<String, dynamic> json) {
    return PremisesKeeper(
      premisesId: json['premises_id'] as int?,
      personId: json['person_id'] as int?,
      startDate: json['start_date'] == null
          ? null
          : DateTime.parse(json['start_date'] as String),
      endDate: json['end_date'] == null
          ? null
          : DateTime.parse(json['end_date'] as String),
      uid: json['uid'] as String?,
      version: json['version'] is int
          ? json['version'] as int
          : (json['version'] != null
              ? int.tryParse(json['version'].toString())
              : null),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'premises_id': premisesId,
      'person_id': personId,
      'start_date': startDate?.toIso8601String(),
      'end_date': endDate?.toIso8601String(),
      'uid': uid,
      'version': version,
    };
  }
}
