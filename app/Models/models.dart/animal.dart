import 'package:isar/isar.dart';
import 'animal_identifier.dart';
import 'person_role.dart';
import 'event.dart';

part 'animal.g.dart';

@Collection()
class Animal {
  Id id = Isar.autoIncrement;

  int? createdBy;
  int? premisesId;
  String? species;
  String? sex;
  DateTime? birthDate;
  String? lifeStatus;
  String? uid;
  int? version;

  DateTime? createdAt;
  DateTime? updatedAt;
  DateTime? deletedAt;

  final creator = IsarLink<dynamic>();
  final premise = IsarLink<dynamic>();
  final identifiers = IsarLinks<AnimalIdentifier>();
  final personRoles = IsarLinks<PersonRole>();
  final events = IsarLinks<Event>();

  Animal({
    this.createdBy,
    this.premisesId,
    this.species,
    this.sex,
    this.birthDate,
    this.lifeStatus,
    this.uid,
    this.version,
    this.createdAt,
    this.updatedAt,
    this.deletedAt,
  });

  factory Animal.fromJson(Map<String, dynamic> json) {
    return Animal(
      createdBy: json['created_by'] as int?,
      premisesId: json['premises_id'] as int?,
      species: json['species'] as String?,
      sex: json['sex'] as String?,
      birthDate: json['birth_date'] == null
          ? null
          : DateTime.parse(json['birth_date'] as String),
      lifeStatus: json['life_status'] as String?,
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
      'created_by': createdBy,
      'premises_id': premisesId,
      'species': species,
      'sex': sex,
      'birth_date': birthDate?.toIso8601String(),
      'life_status': lifeStatus,
      'uid': uid,
      'version': version,
      'created_at': createdAt?.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'deleted_at': deletedAt?.toIso8601String(),
    };
  }
}
